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
 * User: mp
 * Date: 25.08.11
 * Time: 15:01
 */

require_once 'Bee/Cache/Provider/AbstractSerializing.php';
class Bee_Cache_Provider_File extends Bee_Cache_Provider_AbstractSerializing {

	const DATA_FILE_SUFFIX = '.ser';
	const SEMAPHORE_FILE_SUFFIX = '.sem';

	/**
	 * @var string
	 */
	private $cacheDir;

	public function __construct($cacheDir = false) {
		if(!$cacheDir) {
			$cacheDir = sys_get_temp_dir();
		}
		$this->cacheDir = $cacheDir;
	}

	protected function &loadCacheArray() {
		$cacheFile = $this->getCacheFileName();

		if(file_exists($cacheFile . self::DATA_FILE_SUFFIX)) {

			// obtain shared lock on semaphore file
			$fp = fopen($cacheFile . self::SEMAPHORE_FILE_SUFFIX, 'w+');
			if (flock($fp, LOCK_SH)) {
				// shared lock obtained, no one else is writing right now
				$cacheContent = file_get_contents($cacheFile . self::DATA_FILE_SUFFIX);
				fclose($fp);
				$cacheContent = unserialize($cacheContent);
				return $cacheContent;
			}
		}
		return array();
	}

	protected function storeCacheArray(&$array) {
		$cacheFile = $this->getCacheFileName();

		// obtain exclusive lock on semaphore file
		$fp = fopen($cacheFile . self::SEMAPHORE_FILE_SUFFIX, 'w+');
		if (flock($fp, LOCK_EX)) {
			// semaphore is exclusively ours
			file_put_contents($cacheFile . self::DATA_FILE_SUFFIX, serialize($array));
		}
		fclose($fp);
	}

	private function getCacheFileName() {
		$tmpName = $this->cacheDir . DIRECTORY_SEPARATOR . '__BeeFileCache' . (BeeFramework::getApplicationId() ? BeeFramework::getApplicationId() : '');
		return $tmpName;
	}
}