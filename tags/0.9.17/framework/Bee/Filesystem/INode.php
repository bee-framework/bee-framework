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
 * Interface representing a node (file / directory) in the file system. 
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
interface Bee_Filesystem_INode {
	
	/**
	 * Enter description here...
	 *
	 * @return Bee_Filesystem_Manager
	 */
	public function getManager();
	
	/**
	 * Enter description here...
	 *
	 * @return Bee_Filesystem_INode
	 */
	public function getParent();
	
	/**
	 * Enter description here...
	 *
	 * @return String
	 */
	public function getFilename();
	
	/**
	 * Enter description here...
	 *
	 * @return long
	 */
	public function getMTime();
	
	/**
	 * Enter description here...
	 *
	 * @return long
	 */
	public function getCTime();
	
	/**
	 * Enter description here...
	 *
	 * @return long
	 */
	public function getATime();
	
	/**
	 * Enter description here...
	 *
	 * @return unknown_type 
	 */
	public function getInode();
	
	/**
	 * Enter description here...
	 *
	 * @return unknown_type
	 */
	public function getOwner();
	
	/**
	 * Enter description here...
	 *
	 * @return unknown_type
	 */
	public function getPerms();
	
	/**
	 * Enter description here...
	 *
	 * @return String
	 */
	public function getPath();
	
	/**
	 * Enter description here...
	 *
	 * @return String
	 */
	public function getPathName();
	
	/**
	 * Enter description here...
	 *
	 * @return long
	 */
	public function getSize();
	
	/**
	 * Enter description here...
	 *
	 * @return String
	 */
	public function getMimeType();
	
	/**
	 * Enter description here...
	 *
	 * @return boolean
	 */
	public function isDir();
	
	/**
	 * Enter description here...
	 *
	 * @return Iterator <Bee_Filesystem_INode>
	 */
	public function getFiles();

	/**
	 * Enter description here...
	 *
	 * @return Iterator <Bee_Filesystem_INode>
	 */
	public function getDirectories();
	
	/**
	 * Enter description here...
	 *
	 * @param String $name
	 * @return Bee_Filesystem_INode
	 */
	public function createDirectory($name);
	
	/**
	 * Enter description here...
	 *
	 * @param String $fileFieldName
	 */
	public function addUploadedFile($fileFieldName);
	
	/**
	 * Enter description here...
	 *
	 * @param String $newName
	 * @return void
	 */
	public function rename($newName);
	
	/**
	 * Enter description here...
	 *
	 * @param String $newParentPath
	 * @return void
	 */
	public function move($newParentPath);
	
	/**
	 * Enter description here...
	 *
	 * @param String $newParentPath
	 * @return void
	 */
	public function copy($newParentPath);
	
	/**
	 * Enter description here...
	 *
	 * @return void
	 */
	public function delete();
}
?>