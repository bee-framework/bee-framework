<?php
namespace Bee\Cache\Provider;
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

require_once 'Bee/Cache/IProvider.php';
require_once 'Bee/Cache/Provider/ProviderBase.php';
/**
 * Cache provider implementation for the Alternative PHP Cache.
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class ProviderAPC extends ProviderBase {
	
	const APC_CACHE_TYPE = 'user';
	
	public final function listKeys() {
		$info = apc_cache_info(self::APC_CACHE_TYPE);
		return array_map(array($this, 'keyListMapCallback'), $info['cache_list']);
	}

	public final function clearCache() {
        if (function_exists('apc_cache_info') && ($cache=@apc_cache_info('opcode'))) {
            apc_clear_cache('opcode');
        }
        if (function_exists('apc_cache_info') && ($cache=@apc_cache_info('user'))) {
            apc_clear_cache('user');
        }
        return true;
//		return apc_clear_cache(self::APC_CACHE_T YPE);
	}

	public function exists($key) {
		return apc_exists($key);
	}

	protected final function doStore($key, $value, $etime = 0) {
		return apc_store($key, $value, $this->getTTL($etime));
	}

	protected final function doRetrieve($key) {
		return apc_fetch($key);
	}
	
	public final function evict($key) {
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
