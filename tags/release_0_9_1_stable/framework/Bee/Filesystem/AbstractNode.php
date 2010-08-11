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

abstract class Bee_Filesystem_AbstractNode implements Bee_Filesystem_INode {
	
	/**
	 * Enter description here...
	 *
	 * @var Bee_Filesystem_Manager
	 */
	private $manager;

	/**
	 * Enter description here...
	 *
	 * @var string
	 */
	private $realPath;
	
	public function __construct(Bee_Filesystem_Manager $manager, $realPath) {
		$this->manager = $manager;
		$this->realPath = $realPath;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return Bee_Filesystem_Manager
	 */
	public final function getManager() {
		return $this->manager;
	}
	
	public final function getRealPath() {
		return $this->realPath; 
	}

	public final function getParent() {
		$parentPath = $this->realPath;
		// strip last path element from our realPath
		$parentPath = substr($parentPath, 0, strrpos($parentPath, DIRECTORY_SEPARATOR));
		
		if($this->manager->isInBasePath($parentPath)) {
			return $this->createNode($parentPath);
		}
		return false;
	}
	
	public final function createDirectory($name) {
		$newDir = $this->realPath . DIRECTORY_SEPARATOR . $this->manager->cleanName($name);
		if(mkdir($newDir)) {
			$this->afterChildCreate();
			return $this->createNode($newDir);
		} else {
			throw new Bee_Exceptions_FilesystemException(Bee_Exceptions_FilesystemException::MESSAGE_COULD_NOT_CREATE, $newdir);
		}
	}
	
	public final function addUploadedFile($fileFieldName, $fileName = null) {
		if (empty($_FILES[$fileFieldName]["name"])) {
			throw new Bee_Exceptions_FilesystemException(Bee_Exceptions_FilesystemException::MESSAGE_RESOURCE_DOES_NOT_EXIST);
		}
		if (is_null($fileName)) {
			$fileName = $_FILES[$fileFieldName]["name"];
		}
		$newFile = $this->realPath . DIRECTORY_SEPARATOR . $fileName;
		if(file_exists($newFile)) {
			throw new Bee_Exceptions_FilesystemException(Bee_Exceptions_FilesystemException::MESSAGE_RESOURCE_EXISTS, $newFile);
		}
		if (!@move_uploaded_file($_FILES[$fileFieldName]["tmp_name"], $newFile)) {
			throw new Bee_Exceptions_FilesystemException(Bee_Exceptions_FilesystemException::MESSAGE_UNKNOWN_ERROR);
		}
		$this->afterChildCreate();
		return $this->createNode($newFile);		
	}
	
	public final function rename($newName) {
		$newPath = $this->getPath() . DIRECTORY_SEPARATOR . $this->manager->cleanName($newName);
		if(rename($this->realPath, $newPath)) {
			$this->realPath = $newPath;
			$this->afterRename();
		} else {
			throw new Bee_Exceptions_FilesystemException(Bee_Exceptions_FilesystemException::MESSAGE_COULD_NOT_RENAME, $newPath);
		}	
	}
	
	public final function move($newParentPath) {
		$newRealPath = $newParentPath . DIRECTORY_SEPARATOR . $this->getFilename();
		if(rename($this->realPath, $newRealPath)) {
			$this->realPath = $newRealPath;
			$this->afterMove();
		} else {
			throw new Bee_Exceptions_FilesystemException(Bee_Exceptions_FilesystemException::MESSAGE_COULD_NOT_MOVE, $newRealPath);
		}
	}

	public final function copy($newParentPath) {
		$newRealPath = $newParentPath . DIRECTORY_SEPARATOR . $this->getFilename();
		if(copy($this->realPath, $newRealPath)) {
			$this->realPath = $newRealPath;
			$this->afterCopy();
		} else {
			throw new Bee_Exceptions_FilesystemException(Bee_Exceptions_FilesystemException::MESSAGE_COULD_NOT_COPY, $newRealPath);
		}
	}

	public final function delete() {
		
		if($this->manager->getNode()->getPathName() != $this->realPath) {
		
			if($this->isDir()) {
				foreach($this->getDirectories() as $dir) {
					$dir->delete();
				}
				foreach($this->getFiles() as $file) {
					$file->delete();
				}
				$result = rmdir($this->realPath);
			} else {
				$result = unlink($this->realPath);
			}
			if($result) {
				$this->afterDelete();
			} else {
				throw new Bee_Exceptions_FilesystemException(Bee_Exceptions_FilesystemException::MESSAGE_COULD_NOT_DELETE, $this->realPath);
			}
			
		}
		
	}
	
	public function getPathName() {
		return $this->realPath;
	}

	protected abstract function createNode($path);
	
	protected abstract function afterChildCreate();

	protected abstract function afterRename();
	
	protected abstract function afterMove();
	
	protected abstract function afterCopy();
	
	protected abstract function afterDelete();
	
}
?>