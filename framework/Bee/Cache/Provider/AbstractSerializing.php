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

require_once 'Bee/Cache/Provider/Base.php';
abstract class Bee_Cache_Provider_AbstractSerializing extends Bee_Cache_Provider_Base {

	const ETIME_KEY_SUFFIX = '__ETIME__';

	/**
	 * Enter description here...
	 *
	 * @var array
	 */
	private $cache;

	private $modified;

	public final function init() {
		$this->cache =& $this->loadCacheArray();
		if(!is_array($this->cache)) {
			$this->cache = array();
		}
		$this->modified = false;
	}

	public function shutdown() {
		if($this->modified) {
			$this->storeCacheArray($this->cache);
		}
	}

	protected abstract function &loadCacheArray();

	protected abstract function storeCacheArray(&$array);

	public final function listKeys() {
		return array_keys($this->cache);
	}

	public function clearCache() {
		$this->cache = array();
		$this->modified = true;
	}
	
	protected final function doStore($key, $value, $etime = 0) {
		$this->cache[$key] = $value;
		$this->cache[$key . self::ETIME_KEY_SUFFIX] = $etime;
		$this->modified = true;
		return true;
	}

	protected final function doRetrieve($key) {
		if($this->checkExpired($key)) {
			return false;
		}
		return $this->cache[$key];
	}
	
	public final function evict($key) {
		unset($this->cache[$key . self::ETIME_KEY_SUFFIX]);
		unset($this->cache[$key]);
		$this->modified = true;
		return true;
	}

	public function exists($key) {
		if($this->checkExpired($key)) {
			return false;
		}
		return array_key_exists($key, $this->cache);
	}

	private function checkExpired($key) {
		$etime = $this->cache[$key . self::ETIME_KEY_SUFFIX];
		if($etime > 0 && $etime < time()) {
			// expired!!
			$this->evict($key);
			return true;
		}
		return false;
	}
}
?>