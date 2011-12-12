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
 * Cache provider implementation for the LIGHTTPD XCache.
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
require_once 'Bee/Cache/IProvider.php';
require_once 'Bee/Cache/Provider/Base.php';
class Bee_Cache_Provider_XCache extends Bee_Cache_Provider_Base {
	
	public final function listKeys() {
		$info = xcache_list(XC_TYPE_VAR, 0);
		return array_map(array($this, 'keyListMapCallback'), $info['cache_list']);
	}

	public final function clearCache() {
		return xcache_clear_cache(XC_TYPE_VAR, 0);
	}

	public function exists($key) {
		return xcache_isset($key);
	}

	protected final function doStore($key, $value, $etime = 0) {
		return xcache_set($key, $value, $this->getTTL($etime));
	}

	protected final function doRetrieve($key) {
		return xcache_get($key);
	}
	
	public final function evict($key) {
		return xcache_unset($key);
	}
	
	private function keyListMapCallback($entry) {
		return $entry['name'];
	}
}
?>