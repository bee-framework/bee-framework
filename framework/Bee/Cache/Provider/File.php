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

	/**
	 * @var string
	 */
	private $cacheFile = '__BeeFileCache';



	public function __construct($cacheDir = false, $cacheFile = false) {
		if(!$cacheDir) {
			$cacheDir = sys_get_temp_dir();
		}
		$this->cacheDir = $cacheDir;
        if ($cacheFile !== false) {
            $this->cacheFile = $cacheFile;
        }
	}

	protected function &loadCacheArray() {
		$cacheFile = $this->getCacheFileName();
		if (file_exists($cacheFile . self::DATA_FILE_SUFFIX)) {
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
        $dir = $this->cacheDir;
        if (Bee_Utils_Strings::hasText($dir) && !preg_match('#'.DIRECTORY_SEPARATOR.'$#i', $dir)) {
            $dir .= DIRECTORY_SEPARATOR;
        }
		$tmpName = $dir . $this->cacheFile . (Bee_Framework::getApplicationId() ? '_'.Bee_Framework::getApplicationId() : '');
		return $tmpName;
	}

    public function clearCache() {
        parent::clearCache();
        $cacheFile = $this->getCacheFileName();
        if (file_exists($cacheFile . self::DATA_FILE_SUFFIX)) {
            unlink($cacheFile . self::DATA_FILE_SUFFIX);
        }
        if (file_exists($cacheFile . self::SEMAPHORE_FILE_SUFFIX)) {
            unlink($cacheFile . self::SEMAPHORE_FILE_SUFFIX);
        }
        return true;
   	}
}