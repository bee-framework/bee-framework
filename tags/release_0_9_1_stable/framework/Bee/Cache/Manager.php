<?php
/*
 * Copyright 2008-2010 the original author or authors.
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

/**
 * Manager for caching resources in a PHP variable cache. Actual cache access is delegated to
 * a {@link Bee_Cache_IProvider}. This manager has the following responsibilities:
 * <ul>
 * 		<li>check for supported cache types and instantiate the corresponding provider</li>
 * 		<li>facilitate access to cached resources through the {@link Bee_Cache_ICachableResource}
 * 			abstraction interface and corresponding {@link Bee_Cache_Manager::retrieveCachable} method</li>
 * </ul>
 * 
 * Implementations of other cache providers such as Turck MMCache or eAccelerator are planned (should
 * be pretty straightforward anyway).   
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 * @author Benjamin Hartmann
 */
class Bee_Cache_Manager {
	
	const INFO_DATA_KEY = 'data';
	const INFO_IN_CACHE_SINCE_KEY = 'inCacheSince';
	const INFO_CACHE_HIT_KEY = 'cacheHit';
	const INFO_NO_CACHE_KEY = 'noCache';
	
	private static $useSessionCacheFallback = true;
	
	/**
	 * Map PHP cache extension names to class name of the respective cache provider adapter 
	 *
	 * @var array
	 */
	private static $providers = array(
		'apc' => 'Bee_Cache_Provider_APC',
		'XCache' => 'Bee_Cache_Provider_XCache'
	);

	/**
	 * The cache provider in use, if any
	 *
	 * @var Bee_Cache_IProvider
	 */
	private static $provider;
	
	private static function initCacheProvider() {
		// todo: take into account minimum versions for extensions?
		foreach(self::$providers as $extension => $providerClass) {
			if(extension_loaded($extension)) {
				trigger_error("Extension $extension found, using cache provider $providerClass", E_USER_NOTICE);
				self::$provider = new $providerClass(); 
				return;
			}
		}
		trigger_error("No supported cache extensions found", E_USER_NOTICE);
		if(self::$useSessionCacheFallback) {
			trigger_error("Using fallback cache provider Bee_Cache_Provider_Session", E_USER_NOTICE);
			self::$provider = new Bee_Cache_Provider_Session(); 
		}
	}
	
	public static function init() {
		self::initCacheProvider();
		if(!is_null(self::$provider)) {
			self::$provider->init();	
		}
	}
	
	public static function shutdown() {
		self::$provider->shutdown(); 
	}
	
	/**
	 * Enter description here...
	 *
	 * @return Bee_Cache_IProvider
	 */
	public static function getProvider() {
		return self::$provider;
	}
	
	public static function evict($keyOrCachable) {
		if ($keyOrCachable instanceof Bee_Cache_ICachableResource) {
			$keyOrCachable = $keyOrCachable->getKey();
		}
		$keyOrCachable = (BeeFramework::getApplicationId()!==false ? BeeFramework::getApplicationId().'_' : '').$keyOrCachable;
		if(!is_null(self::$provider)) {				
			self::$provider->evict($keyOrCachable);
		}
	}
	
	/**
	 * Enter description here...
	 *
	 * @param Bee_Cache_ICachableResource $resource
	 * @return mixed
	 */
	public static function retrieveCachable(Bee_Cache_ICachableResource $resource, $returnInfoArray = false) {
		if(is_null(self::$provider)) {
			// no cache provider found, no caching or unsupported cache type installed
			$data =& $resource->createContent(); 
			return $returnInfoArray ? array(self::INFO_NO_CACHE_KEY => true, self::INFO_CACHE_HIT_KEY => false, self::INFO_IN_CACHE_SINCE_KEY => false, self::INFO_DATA_KEY => $data) : $data;
		}
		
		// caching supported, check if in cache and not stale
		$key = (BeeFramework::getApplicationId()!==false ? BeeFramework::getApplicationId().'_' : '').$resource->getKey();
		$mtime = $resource->getModificationTimestamp();
		$inCacheSince = self::$provider->getLastUpdateTimestamp($key);
//		$now = microtime(true);
		if($inCacheSince < $mtime) {
			// @todo: provide logging
			// resource not found in cache or stale, re-create and store in cache
			$data =& $resource->createContent();
			self::$provider->store($key, $data, microtime(true));
			$cacheHit = false;
		} else {
			// @todo: provide logging
			// resource in cache is current, fetch from cache
			$data = self::$provider->retrieve($key);
			$cacheHit = true;
		}
		return $returnInfoArray ? array(self::INFO_CACHE_HIT_KEY => $cacheHit, self::INFO_IN_CACHE_SINCE_KEY => $inCacheSince, self::INFO_DATA_KEY => &$data) : $data;
	}
	
}
?>