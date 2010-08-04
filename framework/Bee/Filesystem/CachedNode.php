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
 * Cached representation of a file system node (cf. {@link Bee_Filesystem_INode}). Uses a {@link Bee_Filesystem_CachedNode_Cachable}
 * for actual cache access.
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class Bee_Filesystem_CachedNode extends Bee_Filesystem_AbstractNode {
	
	/**
	 * Enter description here...
	 *
	 * @var array
	 */
	private $nodeInfo;
	
	/**
	 * Enter description here...
	 *
	 * @var boolean
	 */
	private $unchanged;
	
	public function __construct(Bee_Filesystem_Manager $manager, $realPath, $directlyFromCache = false) {
		parent::__construct($manager, $realPath);
		$this->loadNodeInfo($directlyFromCache);
	}
	
	public final function getFilename() {
		return $this->nodeInfo['filename'];
	}
	
	public final function getMTime() {
		return $this->nodeInfo['mTime'];
	}
	
	public final function getCTime() {
		return $this->nodeInfo['cTime'];
	}
	
	public final function getATime() {
		return $this->nodeInfo['aTime'];
	}
	
	public final function getInode() {
		return $this->nodeInfo['inode'];
	}
	
	public final function getOwner() {
		return $this->nodeInfo['owner'];
	}
	
	public final function getPerms() {
		return $this->nodeInfo['perms'];
	}
	
	public final function getPath() {
		return $this->nodeInfo['path'];
	}
	
	public final function getSize() {
		return $this->nodeInfo['size'];
	}
	
	public function getMimeType() {
		return $this->nodeInfo['mimeType'];
	}
	
	public final function isDir() {
		return is_array($this->nodeInfo['dirs']) || is_array($this->nodeInfo['files']);
	}
	
	/**
	 * Enter description here...
	 *
	 * @return Bee_Filesystem_CachedNode_ChildIterator
	 */
	public final function getFiles() {
		return new Bee_Filesystem_CachedNode_ChildIterator($this, $this->nodeInfo['files'], $this->nodeInfo['path'] . DIRECTORY_SEPARATOR . $this->nodeInfo['filename'], $this->unchanged);
	}

	/**
	 * Enter description here...
	 *
	 * @return Bee_Filesystem_CachedNode_ChildIterator
	 */
	public final function getDirectories() {
		trigger_error("ITERATING OVER DIRS OF ". $this->getRealPath(), E_USER_NOTICE);
		return new Bee_Filesystem_CachedNode_ChildIterator($this, $this->nodeInfo['dirs'], $this->nodeInfo['path'] . DIRECTORY_SEPARATOR . $this->nodeInfo['filename'], false);			
	}	
	
	protected final function createNode($path) {
		return new Bee_Filesystem_CachedNode($this->getManager(), $path, false);		
	}

	protected final function afterChildCreate() {
		clearstatcache(); // this might be necessary if the file system cache is accessed before the move operation
		$this->refresh();
	}

	protected final function afterRename() {
//		clearstatcache(); // this might be necessary if the file system cache is accessed before the move operation
		$this->loadNodeInfo(false);		
	}
	
	protected final function afterMove() {
//		clearstatcache(); // this might be necessary if the file system cache is accessed before the move operation
		$this->loadNodeInfo(false);		
	}
	
	protected final function afterCopy() {
//		clearstatcache(); // this might be necessary if the file system cache is accessed before the copy operation
		$this->getParent()->refresh();
	}
	
	protected final function afterDelete() {
		clearstatcache(); // this might be necessary if the file system cache is accessed before the move operation
		Bee_Cache_Manager::evict(Bee_Filesystem_CachedNode_Cachable::createKey($this->getRealPath()));
//		$provider = Bee_Cache_Manager::getProvider();
//		if(!is_null($provider)) {				
//			$provider->evict(Bee_Filesystem_CachedNode_Cachable::createKey($this->getRealPath()));
//			trigger_error("EVICTING ". Bee_Filesystem_CachedNode_Cachable::createKey($this->getRealPath()), E_USER_NOTICE);
//		}
		$this->getParent()->refresh();
	}
	
	protected final function refresh() {
		clearstatcache();
//		trigger_error("REFRESHING ". $this->getRealPath(), E_USER_NOTICE);
		Bee_Cache_Manager::evict(Bee_Filesystem_CachedNode_Cachable::createKey($this->getRealPath()));
//		$provider = Bee_Cache_Manager::getProvider();
//		if(!is_null($provider)) {				
//			$provider->evict(Bee_Filesystem_CachedNode_Cachable::createKey($this->getRealPath()));
//		}
		$this->nodeInfo = null;
		$this->loadNodeInfo(false);
	}
	
	private function loadNodeInfo($directlyFromCache = false) {
		$provider = Bee_Cache_Manager::getProvider();
		$cachable = new Bee_Filesystem_CachedNode_Cachable($this->getRealPath());
		if(is_null($provider)) {
			$this->nodeInfo =& $cachable->createContent();
			$this->unchanged = false;
		} else {
			if($directlyFromCache) {
				$this->nodeInfo = $provider->retrieve($cachable->getKey());
				$this->unchanged = true;
			}
			if(!$this->nodeInfo) {
				$cacheInfo = Bee_Cache_Manager::retrieveCachable($cachable, true);
				$this->nodeInfo =& $cacheInfo[Bee_Cache_Manager::INFO_DATA_KEY];
				$this->unchanged = $cacheInfo[Bee_Cache_Manager::INFO_CACHE_HIT_KEY];	
			}			
		}
	}
}
?>