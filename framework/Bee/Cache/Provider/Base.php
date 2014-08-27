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

require_once 'Bee/Cache/IProvider.php';
/**
 * Abstract base class for some cache provider implementations. Mainly solves the problem of
 * retrieving timestamps from the info output of certain cache providers.
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
abstract class Bee_Cache_Provider_Base implements Bee_Cache_IProvider {
	
	public function init() {}

	public final function store($key, &$value, $etime = 0) {
		return $this->doStoreSerialized($key, $value, $etime);
	}
	
	public final function retrieve($key) {
		return $this->doRetrieveSerialized($key);
	}
	
	public function shutdown() {}
	
	protected abstract function doStore($key, $value, $etime = 0);
	
	protected abstract function doRetrieve($key);
	
	protected function doStoreSerialized($key, &$value, $etime = 0) {
		return $this->doStore($key, serialize($value), $etime);
	}

    protected function doRetrieveSerialized($key) {
		$serialized = $this->doRetrieve($key);
   		$data = @unserialize($serialized);
   		if ($data === false && $serialized !== serialize(false)) {
   			throw new Exception('unserialize failed for key: '.$key);
   		}
   		return $data;
   	}

	protected final function getTTL($etime) {
		return $etime - time();
	}

	function __toString() {
		return get_class($this)."{}";
	}
}
