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
 * Abstraction interface for PHP variable cache providers. Implementations of supported providers
 * currently include:
 * <ul>
 * 	<li>{@link Bee_Cache_Provider_APC}: Alternative PHP Cache</li>
 * 	<li>{@link Bee_Cache_Provider_XCache}: LIGHTTPD XCache</li>
 *  * </ul>
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
interface Bee_Cache_IProvider {
	
	public function init();

	public function store($key, $value);
	
	public function retrieve($key);
	
	public function evict($key);
	
	public function getLastUpdateTimestamp($key);
	
	public function getLastAccessTimestamp($key);
	
	public function listKeys();

	public function clearCache();
	
	public function shutdown();
}
?>