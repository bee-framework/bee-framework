<?php
namespace Bee\Cache;
/*
 * Copyright 2008-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
use Bee\Framework;
use Exception;

/**
 * Manager for caching resources in a PHP variable cache. Actual cache access is delegated to
 * a {@link Bee\Cache\IProvider}. This manager has the following responsibilities:
 * <ul>
 * 		<li>check for supported cache types and instantiate the corresponding provider</li>
 * 		<li>facilitate access to cached resources through the {@link ICachableResource}
 * 			abstraction interface and corresponding {@link \Bee\Cache\Manager::retrieveCachable} method</li>
 * </ul>
 * 
 * Implementations of other cache providers such as Turck MMCache or eAccelerator are planned (should
 * be pretty straightforward anyway).
 *
 * IMPORTANT: Do not rely on class autoloading when creating cache extensions. The cache manager is used in the autoloading
 * process!!!
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 * @author Benjamin Hartmann
 */
class Manager {
	
	const INFO_DATA_KEY = 'data';
	const INFO_IN_CACHE_SINCE_KEY = 'inCacheSince';
	const INFO_CACHE_HIT_KEY = 'cacheHit';
	const INFO_NO_CACHE_KEY = 'noCache';

	const CTIME_KEY_SUFFIX = '__CTIME__';

	private static $useFileCacheFallback = true;

	private static $useSessionCacheFallback = true;

	/**
	 * @var \Logger
	 */
	private static $log;

	/**
	 * @return \Logger
	 */
	protected static function getLog() {
		if (!self::$log) {
			self::$log = Framework::getLoggerForClass(__CLASS__);
		}
		return self::$log;
	}

	/**
	 * Map PHP cache extension names to class name of the respective cache provider adapter
	 *
	 * @var array
	 */
	private static $providers = array(
		'apc' => 'Bee\Cache\Provider\ProviderAPC',
		'XCache' => 'Bee\Cache\Provider\ProviderXCache'
	);

	/**
	 * The cache provider in use, if any
	 *
	 * @var IProvider
	 */
	private static $provider;

	/**
	 *
	 */
	private static function initCacheProvider() {
		// todo: take into account minimum versions for extensions?
		foreach(self::$providers as $extension => $providerClass) {
			if(extension_loaded($extension)) {
				self::initProviderClass($providerClass);
				return;
			}
		}
		if(self::$useFileCacheFallback) {
			self::initProviderClass('Bee\Cache\Provider\ProviderFile');
		} else if(self::$useSessionCacheFallback) {
			self::initProviderClass('Bee\Cache\Provider\ProviderSession');
		}
	}

	/**
	 * @param $providerClass
	 */
	private static function initProviderClass($providerClass) {
		static::getLog()->info('initializing cache provider class ' . $providerClass);
		$loc = Framework::getClassFileLocations($providerClass);
		require_once $loc[0];
		self::$provider = new $providerClass();
		static::getLog()->info('cache initialized : ' . get_class(static::$provider));
	}

	/**
	 * @param bool|false $providerInstanceOrClassName
	 */
	public static function init($providerInstanceOrClassName = false) {
		if($providerInstanceOrClassName) {
			if(is_string($providerInstanceOrClassName)) {
				self::initProviderClass($providerInstanceOrClassName);
			} else if($providerInstanceOrClassName instanceof IProvider) {
				self::$provider = $providerInstanceOrClassName;
			} else {
				// todo: provide logging
			}
		} else {
			self::initCacheProvider();
		}
		if(!is_null(self::$provider)) {
			self::$provider->init();	
		}
	}

	/**
	 *
	 */
	public static function shutdown() {
		if(!is_null(self::$provider)) {
			self::$provider->shutdown();
		}
	}
	
	/**
	 * Enter description here...
	 *
	 * @return IProvider
	 */
	public static function getProvider() {
		return self::$provider;
	}

	/**
	 * @param $keyOrCachable
	 */
	public static function evict($keyOrCachable) {
		if ($keyOrCachable instanceof ICachableResource) {
			$keyOrCachable = $keyOrCachable->getKey();
		}
		$keyOrCachable = self::getQualifiedKey($keyOrCachable);
		if(!is_null(self::$provider)) {				
			self::$provider->evict($keyOrCachable);
		}
	}
	
    /**
   	 * Enter description here...
   	 *
   	 * @param ICachableResource $resource
   	 * @return mixed
   	 */
   	public static function &retrieveCachable(ICachableResource $resource, $returnInfoArray = false) {
   		if(is_null(self::$provider)) {
   			// no cache provider found, no caching or unsupported cache type installed
   			$data =& $resource->createContent();
   			$result = $returnInfoArray ? array(self::INFO_NO_CACHE_KEY => true, self::INFO_CACHE_HIT_KEY => false, self::INFO_IN_CACHE_SINCE_KEY => false, self::INFO_DATA_KEY => &$data) : $data;
   			return $result;
   		}

   		// caching supported, check if in cache and not stale
   		$key = self::getQualifiedKey($resource->getKey());
   		$ctimeKey = $key . self::CTIME_KEY_SUFFIX;

   		try {
   			$inCacheSince = self::$provider->retrieve($ctimeKey);
   			$inCacheSince = $inCacheSince === false ? -1 : $inCacheSince;

   		} catch (Exception $e) {
   			$inCacheSince = -1;
   		}

   		$mtime = $resource->getModificationTimestamp();

   		if($inCacheSince < $mtime) {
   			// @todo: provide logging
   			// resource not found in cache or stale, re-create and store in cache

   			$etime = 0;
   			$data =& $resource->createContent($etime);

   			self::$provider->store($ctimeKey, $mtime, $etime);
   			self::$provider->store($key, $data, $etime);

   			$cacheHit = false;
   		} else {
   			// @todo: provide logging
   			// resource in cache is current, fetch from cache
   			try {
   				$data = self::$provider->retrieve($key);
   				$cacheHit = true;

   			} catch (Exception $e) {
                // ACHTUNG!!!!!!!!
                // ACHTUNG!!!!!!!!
                // ACHTUNG!!!!!!!!
                // ACHTUNG!!!!!!!!
                //
                // hier habe ich einfach ein stück code aus dem if ... zweig dupliziert
                //
                // @todo: provide logging
   				// resource not found in cache or stale, re-create and store in cache
   				$etime = 0;
   				$data =& $resource->createContent($etime);

   				self::$provider->store($ctimeKey, $mtime, $etime);
   				self::$provider->store($key, $data, $etime);

   				$cacheHit = false;
   			}
   		}

   		if($returnInfoArray) {
   			$data = array(self::INFO_CACHE_HIT_KEY => $cacheHit, self::INFO_IN_CACHE_SINCE_KEY => $inCacheSince, self::INFO_DATA_KEY => &$data);
   		}
   		return $data;
   	}

    /**
     * Enter description here...
     *
     * @param string $key
     * @param callback $contentCreator
     * @param int $lastModified
     * @param bool $returnInfoArray
     * @return mixed
     */
   	public static function &retrieveCachableClosure($key, $contentCreator, $lastModified = 0, $returnInfoArray = false) {
   		if(is_null(self::$provider)) {
   			// no cache provider found, no caching or unsupported cache type installed
   			$data =& $contentCreator();
   			$result = $returnInfoArray ? array(self::INFO_NO_CACHE_KEY => true, self::INFO_CACHE_HIT_KEY => false, self::INFO_IN_CACHE_SINCE_KEY => false, self::INFO_DATA_KEY => &$data) : $data;
   			return $result;
   		}

   		// caching supported, check if in cache and not stale
   		$key = self::getQualifiedKey($key);
   		$ctimeKey = $key . self::CTIME_KEY_SUFFIX;

   		try {
   			$inCacheSince = self::$provider->retrieve($ctimeKey);
   			$inCacheSince = $inCacheSince === false ? -1 : $inCacheSince;

   		} catch (Exception $e) {
   			$inCacheSince = -1;
   		}

   		$mtime = $lastModified;

   		if($inCacheSince < $mtime) {
   			// @todo: provide logging
   			// resource not found in cache or stale, re-create and store in cache

   			$etime = 0;
   			$data =& $contentCreator($etime);

   			self::$provider->store($ctimeKey, $mtime, $etime);
   			self::$provider->store($key, $data, $etime);

   			$cacheHit = false;
   		} else {
   			// @todo: provide logging
   			// resource in cache is current, fetch from cache
   			try {
   				$data = self::$provider->retrieve($key);
   				$cacheHit = true;

   			} catch (Exception $e) {
                // ACHTUNG!!!!!!!!
                // ACHTUNG!!!!!!!!
                // ACHTUNG!!!!!!!!
                // ACHTUNG!!!!!!!!
                //
                // hier habe ich einfach ein stück code aus dem if ... zweig dupliziert
                //
                // @todo: provide logging
   				// resource not found in cache or stale, re-create and store in cache
   				$etime = 0;
                $data =& $contentCreator($etime);

   				self::$provider->store($ctimeKey, $mtime, $etime);
   				self::$provider->store($key, $data, $etime);

   				$cacheHit = false;
   			}
   		}

   		if($returnInfoArray) {
   			$data = array(self::INFO_CACHE_HIT_KEY => $cacheHit, self::INFO_IN_CACHE_SINCE_KEY => $inCacheSince, self::INFO_DATA_KEY => &$data);
   		}
   		return $data;
   	}

	/**
	 * @param $key
	 * @return mixed
	 */
	public static function retrieve($key) {
		return self::$provider->retrieve(self::getQualifiedKey($key));
	}

	/**
	 * @static
	 * @param $key string
	 * @return bool
	 */
	public static function exists($key) {
		if(is_null(self::$provider)) {
			return false;
		}
		return self::$provider->exists($key);
	}

	/**
	 * @param $key
	 * @param $value
	 * @param int $etime
	 */
	public static function store($key, &$value, $etime = 0) {
		if(!is_null(self::$provider)) {
			self::$provider->store(self::getQualifiedKey($key), $value, $etime);
		}
	}

	/**
	 * @param $key
	 * @return string
	 */
	private static function getQualifiedKey($key) {
		return (Framework::getApplicationId()!==false ? Framework::getApplicationId().'_' : '') . $key;
	}

	/**
	 * @return mixed
	 */
    public static function clearCache() {
		if(is_null(self::$provider)) {
			return false;
		}
        return self::$provider->clearCache();
    }

	/**
	 * @param boolean $useFileCacheFallback
	 */
	public static function setUseFileCacheFallback($useFileCacheFallback) {
		self::$useFileCacheFallback = $useFileCacheFallback;
	}

	/**
	 * @param boolean $useSessionCacheFallback
	 */
	public static function setUseSessionCacheFallback($useSessionCacheFallback) {
		self::$useSessionCacheFallback = $useSessionCacheFallback;
	}
}
