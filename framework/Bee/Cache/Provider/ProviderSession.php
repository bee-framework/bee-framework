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

require_once 'Bee/Cache/Provider/AbstractSerializingProvider.php';
class ProviderSession extends AbstractSerializingProvider {

	const SESSION_CACHE_KEY = '__BeeFrameworkSessionCache';

	protected function &loadCacheArray() {
		session_start();
		return $_SESSION[self::SESSION_CACHE_KEY];
	}

	protected function storeCacheArray(&$array) {
		$_SESSION[self::SESSION_CACHE_KEY] &= $array;
	}

    public function clearCache() {
        parent::clearCache();
        unset($_SESSION[self::SESSION_CACHE_KEY]);
   	}
}
