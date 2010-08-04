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
 * Abstract base class for some cache provider implementations. Mainly solves the problem of
 * retrieving timestamps from the info output of certain cache providers.
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
abstract class Bee_Cache_Provider_Base {
	
	const CTIME_KEY_SUFFIX = '__CTIME__';
	const ATIME_KEY_SUFFIX = '__ATIME__';
	
	public function init() {}

	public final function store($key, $value, $mtime = 0) {
		$mtime = $mtime == 0 ? time() : $mtime;
		$this->doStore($key . self::CTIME_KEY_SUFFIX, $mtime);
		$this->doStore($key . self::ATIME_KEY_SUFFIX, $mtime);
		return $this->doStoreSerialized($key, $value);
	}
	
	public final function retrieve($key) {
		$this->doStore($key . self::ATIME_KEY_SUFFIX, time());
		return $this->doRetrieveSerialized($key);
	}
	
	public final function evict($key) {
		$this->doEvict($key . self::CTIME_KEY_SUFFIX);
		$this->doEvict($key . self::ATIME_KEY_SUFFIX);
		return $this->doEvict($key);
	}
	
	public final function getLastUpdateTimestamp($key) {
		return $this->doRetrieve($key . self::CTIME_KEY_SUFFIX);
	}
	
	public final function getLastAccessTimestamp($key) {
		return $this->doRetrieve($key . self::ATIME_KEY_SUFFIX);
	}
	
	public function shutdown() {}
	
	protected abstract function doStore($key, $value);
	
	protected abstract function doRetrieve($key);
	
	protected abstract function doEvict($key);

	protected function doStoreSerialized($key, $value) {
		return $this->doStore($key, serialize($value));
	}

	protected function doRetrieveSerialized($key) {
		return unserialize($this->doRetrieve($key));
	}
	
}
?>