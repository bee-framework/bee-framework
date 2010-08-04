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

class Bee_Cache_Provider_Session extends Bee_Cache_Provider_Base implements Bee_Cache_IProvider {
	
	const SESSION_CACHE_KEY = '__BeeFrameworkSessionCache';
	const SESSION_CACHE_META_KEY = '__BeeFrameworkSessionCache_Meta';
	
	/**
	 * Enter description here...
	 *
	 * @var array
	 */
	private $cache;

	public function init() {
		session_start();
		$this->cache = $_SESSION[self::SESSION_CACHE_KEY];
		if(!is_array($this->cache)) {
			$this->cache = array();
		}
	}
	
	public final function listKeys() {
		return array_keys($this->cache);
	}

	public function clearCache() {
		$this->cache = array();
		$_SESSION[self::SESSION_CACHE_KEY] = $this->cache;
	}
	
	public function shutdown() {
		$_SESSION[self::SESSION_CACHE_KEY] = $this->cache;
	}

	protected final function doStore($key, $value) {
		$this->cache[$key] = $value;
		$_SESSION[self::SESSION_CACHE_KEY] = $this->cache;		
		return true;
	}

	protected final function doRetrieve($key) {
		return $this->cache[$key];
	}
	
	protected final function doEvict($key) {
		unset($this->cache[$key]);
		return true;
	}	
}
?>