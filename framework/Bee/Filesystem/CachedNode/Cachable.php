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
 * Cache helper for {@link Bee_Filesystem_CachedNode}.
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class Bee_Filesystem_CachedNode_Cachable implements Bee_Cache_ICachableResource {
	
	const CACHE_KEY_PREFIX = 'FSMANAGER_NODEINFO_';
	
	/**
	 * Enter description here...
	 *
	 * @var string
	 */
	private $dir;
	
	public function __construct($dir) {
		$this->dir = $dir;
	}

	public final function getKey() {
		return self::createKey($this->dir);
	}
	
	public final function getModificationTimestamp() {
//		trigger_error('###### CHECKTIME ' . $this->dir, E_USER_NOTICE);
		return max(array(filectime($this->dir), filemtime($this->dir)));
	}
	
	public final function &createContent(&$expirationTimestamp = 0) {
		$nodeInfo = array();
		$fileInfo = new SplFileInfo($this->dir);
//		trigger_error('###### INST SplFileInfo', E_USER_NOTICE);

		$nodeInfo['filename'] = $fileInfo->getFilename();
        try {
            $nodeInfo['mTime'] = $fileInfo->getMTime();
            $nodeInfo['cTime'] = $fileInfo->getCTime();
            $nodeInfo['aTime'] = $fileInfo->getATime();
            $nodeInfo['inode'] = $fileInfo->getInode();
            $nodeInfo['owner'] = $fileInfo->getOwner();
            $nodeInfo['perms'] = $fileInfo->getPerms();
        } catch (Exception $e) {
        }
		$nodeInfo['path'] = $fileInfo->getPath();
		$nodeInfo['mimeType'] = Bee_Filesystem_MimeDetector::detect($nodeInfo['filename']);
		
		if($fileInfo->isDir()) {
			$dirs = array();
			$files = array();

			$iter = new DirectoryIterator($this->dir);
//			trigger_error('###### INST DirectoryIterator', E_USER_NOTICE);
			foreach($iter as $idx => $info) {
				$filename = $info->getFilename();
				if($filename == '.' || $filename == '..') {
					continue;
				}
				if($info->isDir()) {
					$dirs[] = $filename;
				} else {
					$files[] = $filename;
				}
			}
			
			$nodeInfo['dirs'] =& $dirs;
			$nodeInfo['files'] =& $files;
		} else {
            try {
                $nodeInfo['size'] = $fileInfo->getSize();
            } catch (Exception $e) {
            }
		}
		
		return $nodeInfo;
	}
	
	public static function createKey($dir) {
		return self::CACHE_KEY_PREFIX . $dir;
	}
}
?>