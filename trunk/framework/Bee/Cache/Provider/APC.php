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
 * Cache provider implementation for the Alternative PHP Cache.
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class Bee_Cache_Provider_APC extends Bee_Cache_Provider_Base implements Bee_Cache_IProvider {
	
	const APC_CACHE_TYPE = 'user';
	
	public final function listKeys() {
		$info = apc_cache_info(self::APC_CACHE_TYPE);
		return array_map(array($this, 'keyListMapCallback'), $info['cache_list']);
	}

	public final function clearCache() {
		return apc_clear_cache(self::APC_CACHE_TYPE);
	}

	protected final function doStore($key, $value) {
		return apc_store($key, $value);
	}

	protected final function doRetrieve($key) {
		return apc_fetch($key);
	}
	
	protected final function doEvict($key) {
		return apc_delete($key);
	}
	
	private function keyListMapCallback($entry) {
		return $entry['info'];
	}

//	protected final function doStoreSerialized($key, $value) {
//		return apc_store($key, $value);
//	}
//
//	protected final function doRetrieveSerialized($key) {
//		return apc_fetch($key);
//	}	
}
?>