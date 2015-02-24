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

use Bee\Exceptions\FilesystemException;

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
			throw new FilesystemException(FilesystemException::MESSAGE_COULD_NOT_CREATE, $newDir);
		}
	}

    public final function addFile($sourceFile, $filename, $overwrite=false) {
        if (!file_exists($sourceFile)) {
            throw new Exception('File not found: '.$sourceFile);
        }

        $newFile = $this->realPath;
        $newFile .= $newFile[strlen($newFile)-1]!=DIRECTORY_SEPARATOR ? DIRECTORY_SEPARATOR : '';

        if (!$overwrite) {
            $piName = pathinfo($filename, PATHINFO_FILENAME);
            $piExt = pathinfo($filename, PATHINFO_EXTENSION);
            $filename = $piName.'.'.$piExt;
            $count = 0;
            while (file_exists($newFile.$filename)) {
                $count++;
                $filename = $piName.'-'.$count.'.'.$piExt;
            }
        }

        $newFile .= $filename;

        if (is_uploaded_file($sourceFile)) {
            move_uploaded_file($sourceFile, $newFile);

        } else {
            file_put_contents($newFile, fopen('php://input', 'r'));
        }

        $this->afterChildCreate();
        return $this->createNode($newFile);
    }

	public final function addUploadedFile($fileFieldName, $fileName = null) {
        
        echo 'SAVE UPLOADED FILE INTO: ';
        echo $this->getPathName();
        echo DIRECTORY_SEPARATOR;
        echo $fileName;
        echo '<hr/>';
        
        if (isset($_GET['qqfile'])) {
            $this->addUploadedFileFromXhr($fileFieldName, $fileName);

        } else if (isset($_FILES['qqfile'])) {
            $this->addUploadedFileFromForm($fileFieldName, $fileName);

        } else {
            throw new Exception("No file to upload");
        }
    }

	public final function addUploadedFileFromForm($fileFieldName, $filename, $overwrite=false) {
        echo 'SAVE FROM FORM<hr/>';
        echo 'Filname: '.$filename.'<br/>';
        echo 'FULLPATH: ';
//        echo $this->getPathName();
        echo $this->realPath();
        echo DIRECTORY_SEPARATOR;
        echo $filename;
        echo '<hr/>';


		$newFile = $this->realPath . DIRECTORY_SEPARATOR . $filename;
//		if(file_exists($newFile)) {
//			throw new FilesystemException(FilesystemException::MESSAGE_RESOURCE_EXISTS, $newFile);
//		}

        var_dump($newFile);


//		if (empty($_FILES[$fileFieldName]["name"])) {
//			throw new FilesystemException(FilesystemException::MESSAGE_RESOURCE_DOES_NOT_EXIST);
//		}
//		if (is_null($fileName)) {
//			$fileName = $_FILES[$fileFieldName]["name"];
//		}
//		$newFile = $this->realPath . DIRECTORY_SEPARATOR . $fileName;
//		if(file_exists($newFile)) {
//			throw new FilesystemException(FilesystemException::MESSAGE_RESOURCE_EXISTS, $newFile);
//		}
//		if (!@move_uploaded_file($_FILES[$fileFieldName]["tmp_name"], $newFile)) {
//			throw new FilesystemException(FilesystemException::MESSAGE_UNKNOWN_ERROR);
//		}
//		$this->afterChildCreate();
//		return $this->createNode($newFile);
	}
	
	public final function addUploadedFileFromXhr($fileFieldName, $filename, $overwrite=false) {
        $newFile = $this->realPath.DIRECTORY_SEPARATOR.$filename;

        if (!$overwrite) {
            $piName = pathinfo($filename, PATHINFO_FILENAME);
            $piExt = pathinfo($filename, PATHINFO_EXTENSION);
            $count = 0;
            while (file_exists($newFile)) {
                $count++;
                $newFile = $this->realPath.DIRECTORY_SEPARATOR.$piName.'-'.$count.'.'.$piExt;
            }
        }

        if (isset($_SERVER["CONTENT_LENGTH"])){
            $filesize = intval($_SERVER["CONTENT_LENGTH"]);
        } else {
            throw new Exception('Getting content length is not supported.');
        }

        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);

        if ($realSize != $filesize){
            return false;
        }

        $target = fopen($newFile, "w");
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);

        $this->afterChildCreate();
        return $this->createNode($newFile);
	}

	public final function rename($newName) {
		$newPath = $this->getPath() . DIRECTORY_SEPARATOR . $this->manager->cleanName($newName);
		if(rename($this->realPath, $newPath)) {
			$this->realPath = $newPath;
			$this->afterRename();
		} else {
			throw new FilesystemException(FilesystemException::MESSAGE_COULD_NOT_RENAME, $newPath);
		}	
	}
	
	public final function move($newParentPath) {
		$newRealPath = $newParentPath . DIRECTORY_SEPARATOR . $this->getFilename();
		if(rename($this->realPath, $newRealPath)) {
			$this->realPath = $newRealPath;
			$this->afterMove();
		} else {
			throw new FilesystemException(FilesystemException::MESSAGE_COULD_NOT_MOVE, $newRealPath);
		}
	}

	public final function copy($newParentPath) {
		$newRealPath = $newParentPath . DIRECTORY_SEPARATOR . $this->getFilename();
		if(copy($this->realPath, $newRealPath)) {
			$this->realPath = $newRealPath;
			$this->afterCopy();
		} else {
			throw new FilesystemException(FilesystemException::MESSAGE_COULD_NOT_COPY, $newRealPath);
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
				throw new FilesystemException(FilesystemException::MESSAGE_COULD_NOT_DELETE, $this->realPath);
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