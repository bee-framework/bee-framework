<?php
/*
 * Copyright 2008-2014 the original author or authors.
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
use Bee\MVC\IHttpRequest;
use Bee\MVC\ModelAndView;
use Bee\Utils\Assert;
use Bee\Utils\Strings;

/**
 * Manages access to the servers local filesystem.
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 * @author Benjamin Hartmann
 */
class Bee_Filesystem_HandlerDelegate_Generic {

	const FOLDER_TREE_ROOT_FOLDER_KEY = 'folderTreeRootFolder';
	const FOLDER_TREE_STATE_KEY = 'folderTreeState';
	const FOLDER_TREE_STATE_LEAD_SELECTION_ID_KEY = 'leadSelectionId';
	
	const FILE_LIST_CURRENT_FOLDER_KEY = 'currentFileListFolder';
	const FILE_LIST_STATE_KEY = 'fileListState';
	const FILE_LIST_FILES_KEY = 'files';
	
	const FILE_UPLOAD_FIELD = 'fileManagerUpload';

	private static $messagebundle = array(
		FilesystemException::MESSAGE_COULD_NOT_CREATE => 'Erstellen fehlgeschlagen',
		FilesystemException::MESSAGE_COULD_NOT_DELETE => 'Löschen fehlgeschlagen',
		FilesystemException::MESSAGE_COULD_NOT_MOVE => 'Verschieben fehlgeschlagen',
		FilesystemException::MESSAGE_COULD_NOT_RENAME => 'Umbenennen fehlgeschlagen',
		FilesystemException::MESSAGE_RESOURCE_DOES_NOT_EXIST => 'Datei / Verzeichnis nicht vorhanden',
		FilesystemException::MESSAGE_RESOURCE_EXISTS => 'Datei / Verzeichnis bereits vorhanden',
		FilesystemException::MESSAGE_UNKNOWN_ERROR => 'Unbekannter Fehler'
	);
	
	/**
	 * @var Bee_Filesystem_Manager
	 */
	private $fileManager;
	
	/**
	 * @param Bee_Filesystem_Manager $fileManager
	 */
	public final function setFileManager(Bee_Filesystem_Manager $fileManager) {
		$this->fileManager = $fileManager;
	}
	
	/**
	 * @return Bee_Filesystem_Manager
	 */
	public final function getFileManager() {
		return $this->fileManager;
	}
	
	private final function selectCurrentNode($dirName) {
		$dirName = $this->fileManager->getBasePath().'/'.rawurldecode($dirName);
		return $this->fileManager->getNode($dirName);
	}
	
	public function show(IHttpRequest $request) {
		throw new Exception("Operation not supported");
	}
	
	/*********************************************************************************************************************************************************************************
	 * 
	 * FOLDER TREE
	 * 
	 ********************************************************************************************************************************************************************************/
	
	/**
	 * Enter description here...
	 *
	 * @param IHttpRequest $request
	 * @return ModelAndView
	 * 
	 * @Bee_MVC_Controller_Multiaction_RequestHandler(httpMethod = "GET", pathPattern = "/**\/tree")
	 */
	public final function getFolderTree(IHttpRequest $request) {
		$model = array();
		$requestedFolderName = $request->getParameter(self::FILE_LIST_CURRENT_FOLDER_KEY);
		$folders = array();
		try {
			$currentNode = (!is_null($requestedFolderName)) ? $this->selectCurrentNode($requestedFolderName) : $this->selectRootNode($request);
			$dirs = $currentNode->getDirectories();
			foreach($dirs as $dir) {
				$folders[] = array(
					'label' => $dir->getManager()->toClientEncoding($dir->getFilename()),
					'path' => $dir->getManager()->getRelativePath($dir)
				);
			}
		} catch (Exception $e) {
		}
		
		$model['folders'] = $folders;
		return new ModelAndView($model, 'service.json');
	}
	
	/**
	 * Enter description here...
	 *
	 * @param IHttpRequest $request
	 * @return ModelAndView
	 * 
	 * @Bee_MVC_Controller_Multiaction_RequestHandler(httpMethod = "POST", pathPattern = "/**\/tree")
	 * @Bee_MVC_Controller_Multiaction_RequestHandler(httpMethod = "GET", pathPattern = "/**\/testTree")
	 */
	public final function handleTreeAction(IHttpRequest $request) {
		$command = $request->getParameter('command');
		
		switch ($request->getParameter('command')) {
			case 'createFolder' :
				return $this->doCreateFolder($request);
			case 'deleteFolder' :
				return $this->doDeleteFolder($request);
		}
		
		$model['commandResult'] = 'failure';
		return new ModelAndView($model, 'service.json');
	}
	
	
	/**
	 * Handle create folder action
	 *
	 * @param IHttpRequest $request
	 * @return ModelAndView
	 */
	private function doCreateFolder(IHttpRequest $request) {
		try {
			$requestedFolderName = $request->getParameter(self::FILE_LIST_CURRENT_FOLDER_KEY);
			$currentNode = (!is_null($requestedFolderName)) ? $this->selectCurrentNode($requestedFolderName) : $this->selectRootNode($request);
			
			$newFolderName = $request->getParameter('folderName');
			if (!Strings::hasText($newFolderName)) {
				throw new Exception('No folder name specified for create folder action');
			}

//			$newNode = $this->doCreate($currentNode, $newFolderName);
			$newNode = $currentNode->createDirectory($this->fileManager->toFilesystemEncoding($newFolderName));
			
			if ($request->getParameter('loadFolderContent')=='true') {
				$mav = $this->getFolderTree($request);
				$model = $mav->getModel();
				$mav->addModelValue('commandResult', 'success');
				return $mav;
			} else {
				$model = array();
				$folders[] = array(
					'label' => $this->fileManager->toClientEncoding($newNode->getFilename()),
					'path' => $this->fileManager->getRelativePath($newNode)
				);
				$model['commandResult'] = 'success';
				$model['folders'] = $folders;
				return new ModelAndView($model, 'service.json');
			}
		} catch (FilesystemException $fex) {
			//return $this->relayExceptionMessage($mav, $fex);
			return $mav;
		} catch (Exception $e) {
		}
	}
	
	/**
	 * Handle delete folder action
	 *
	 * @param IHttpRequest $request
	 * @return ModelAndView
	 */
	private function doDeleteFolder(IHttpRequest $request) {
		try {
			$requestedFolderName = $request->getParameter(self::FILE_LIST_CURRENT_FOLDER_KEY);
			if (!Strings::hasText($requestedFolderName)) {
				throw new Exception();
			}
			$currentNode = (!is_null($requestedFolderName)) ? $this->selectCurrentNode($requestedFolderName) : $this->selectRootNode($request);
			
			if (!$this->fileManager->isRootNode($currentNode)) {
				$currentNode->delete();
				$model['commandResult'] = 'success';
				return new ModelAndView($model, 'service.json');
			}
			
			$model['commandResult'] = 'failure';
			return new ModelAndView($model, 'service.json');
		} catch (FilesystemException $fex) {
			return $this->relayExceptionMessage($mav, $fex);
		} catch (Exception $e) {
		}
	}
	
	private function doMove(array $move) {
		Assert::isTrue($move['relative'] == 'into', 'Folder tree does not support reordering. Cannot accept relative positions other than \'into\'.');
		$subjectPath = $move['subject'];
		$subjectPath = rawurldecode($subjectPath);
		$subject = $this->fileManager->getNode($subjectPath);
		if($subject) {
			$targetPath = $move['target'];
			$targetPath = rawurldecode($targetPath);
			if(Strings::hasText($targetPath)) {
				$subject->move($targetPath);
			}
		} // else maybe raise some error? or not, an exception should already be thrown by the fileManager...
	}



	/*********************************************************************************************************************************************************************************
	 * 
	 * FILE LIST
	 * 
	 ********************************************************************************************************************************************************************************/
	
	/**
	 * Enter description here...
	 *
	 * @param IHttpRequest $request
	 * @return ModelAndView
	 * 
	 */
	private final function showFileList(IHttpRequest $request) {
		
		// @TODO: Check out FileManager's formatter
		
		function formatKB($size) {
			$result = $size;// / 1000;
			$result = number_format($result, 0, ',', '.');
			return $result;
		}
		
		function formatMB($size) {
			$result = $size / 1024; // / 1000;
			$result = number_format($result, 1, ',', '.');
			return $result;
		}
	
		function formatFileSize($size) {
			//$size = filesize($file)/1000;
			$size = $size/1000;
			if ($size < 1024) {
				$result  = formatKB($size);
				$result .= ' KB';
			} else {
				$result  = formatMB($size);
				$result .= ' MB';
			}
			return $result;
		}
			

		
		$model = array();
		$requestedFolderName = $request->getParameter(self::FILE_LIST_CURRENT_FOLDER_KEY);
		
		$files = array();
		try {
			$currentNode = (!is_null($requestedFolderName)) ? $this->selectCurrentNode($requestedFolderName) : $this->selectRootNode($request);
			
			foreach($currentNode->getFiles() as $file) {
				$id = rawurlencode($file->getPathName());
				$filename = $file->getManager()->toClientEncoding($file->getFilename());
				$createDate = date('d.m.Y H:i:s', $file->getCTime());
				$modifiedDate = date('d.m.Y H:i:s', $file->getMTime());
				$fileSize = formatFileSize($file->getSize());
				$mimeType = $file->getMimeType();
				
				$files[] = array(
					'id' => $id,
					'icon' => '[i]',
//					'icon' => '<img src="http://tbn0.google.com/images?q=tbn:yiCoPQTChIrjVM:http://www.pantherkut.com/wp-content/uploads/2007/06/fat_cat_4.jpg" />',
					'filename' => $filename,
					'createDate' => $createDate,
					'modifiedDate' => $modifiedDate,
					'fileSize' => $fileSize,
					'mimeType' => $mimeType
				);
			}
		} catch (Exception $e) {
		}
		
		$model['files'] = $files;
		return new ModelAndView($model, 'service.json');
	}
	
	/**
	 * Enter description here...
	 *
	 * @param IHttpRequest $request
	 * @return ModelAndView
	 * 
	 * @Bee_MVC_Controller_Multiaction_RequestHandler(httpMethod = "GET", pathPattern = "/**\/filesExist")
	 */
	public final function filesExist(IHttpRequest $request) {
		$existingFiles = array();
		try {
			//$files = explode(',', $request->getParameter('files'));
			$files = $request->getParameterValues('files');
			$folder = $request->getParameter(self::FILE_LIST_CURRENT_FOLDER_KEY);
			
			$currentFolderNode = $this->selectCurrentNode($folder);
			
			$basePath = $currentFolderNode->getPath().DIRECTORY_SEPARATOR.$currentFolderNode->getFilename().DIRECTORY_SEPARATOR;
			
			foreach ($files as $filenName) {
				try {
					$fNode = $this->fileManager->getNode($basePath.$filenName);
					array_push($existingFiles, $filenName);
					
				} catch (Exception $e) {
				}
			}
		} catch (Exception $e) {
		}
		
		$model = array();
		$model['filesExist'] = false;
		$model['filesCount'] = count($existingFiles);
		if (is_array($existingFiles) && count($existingFiles)>0) {
			$model['filesExist'] = true;
			$model['existingFiles'] = $existingFiles;
		}
		return new ModelAndView($model, 'service.json');
	}
	
	/**
	 * Enter description here...
	 *
	 * @param IHttpRequest $request
	 * @return ModelAndView
	 * 
	 * @Bee_MVC_Controller_Multiaction_RequestHandler(pathPattern = "/**\/filelist")
	 * @Bee_MVC_Controller_Multiaction_RequestHandler(httpMethod = "POST", pathPattern = "/**\/filelist")
	 */
	public final function handleFilesAction(IHttpRequest $request) {
		$command = $request->getParameter('command');
		
		switch ($command) {
			case 'deleteFiles' :
				return $this->doDeleteFiles($request);
				
		}
		
		return $this->showFileList($request);
		/*
		try {
//			$move = $request->getParameterValues('move');
//			if(is_array($move) && count($move) == 3) {
//				$this->doMove($move);
//			}

			$delete = $request->getParameterValues('toDelete');
			if(is_array($delete) && count($delete) > 0) {
				$this->doDelete($delete);
			}			
		
		} catch (FilesystemException $fex) {
			return $this->relayExceptionMessage($this->showFileList($request), $fex);
		}
		
		return $this->showFileList($request);
		*/
		
	}
	
	/**
	 * Handle delete folder action
	 *
	 * @param IHttpRequest $request
	 * @return ModelAndView
	 */
	private function doDeleteFiles(IHttpRequest $request) {
		try {
			$toDeleteParam = $request->getParameter(self::FILE_LIST_FILES_KEY);
			$toDelete = explode(',', $toDeleteParam);
			
			$result = 'success'; 
			foreach ($toDelete as $file) {
				try {
					$currentNode = $this->fileManager->getNode($file);
					if ($currentNode->isDir()) {
						throw new Exception('Expected File but gor Directory');
					}
					$currentNode->delete();
				} catch (Exception $e) {
					$result = 'failure';
				}
			}
			$model['commandResult'] = $result;
		} catch (FilesystemException $fex) {
		} catch (Exception $e) {
		}
		return $this->showFileList($request);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param IHttpRequest $request
	 * @return ModelAndView
	 * 
	 * @Bee_MVC_Controller_Multiaction_RequestHandler(httpMethod = "POST", pathPattern = "/**\/upload")
	 * @Bee_MVC_Controller_Multiaction_RequestHandler(httpMethod = "GET", pathPattern = "/**\/upload")
	 */
	public final function handleFileUpload(IHttpRequest $request) {
		$model = array();
		try {
			$requestedFolderName = $request->getParameter(self::FILE_LIST_CURRENT_FOLDER_KEY);
			$currentNode = (!is_null($requestedFolderName)) ? $this->selectCurrentNode($requestedFolderName) : $this->selectRootNode($request);
			
			$fileName = $this->fileManager->toFilesystemEncoding($_FILES[self::FILE_UPLOAD_FIELD]['name']);

			try {
				$currentNode->addUploadedFile(self::FILE_UPLOAD_FIELD, $fileName);
				
			} catch (FilesystemException $e) {
				// delete file if it already exists
				if ($e->getMessage()==FilesystemException::MESSAGE_RESOURCE_EXISTS) {
					$existingFile = $currentNode->getPath().DIRECTORY_SEPARATOR.$currentNode->getFilename().DIRECTORY_SEPARATOR.$fileName;
					$exFiNode = $this->fileManager->getNode($existingFile);
					$exFiNode->delete();
					$currentNode->addUploadedFile(self::FILE_UPLOAD_FIELD, $fileName);
				}
			} catch (Exception $e) {
			}
			
		} catch (FilesystemException $fex) {
			$errorMessage = $fex->getMessage();
		}

		$model['result'] = $errorMessage ? 'failure' : 'success';
		$model['error'] = self::$messagebundle[$errorMessage];
		
		/*
		$fc = file_get_contents('/UPLOADERLOG.txt');
		$nfc = 'Added file: '.$fileName.' into ';
		if ($currentNode) {
			$nfc .= $currentNode->getPath().'/'.$currentNode->getFilename();
		} else {
			$nfc .= $requestedFolderName.' (Failed)';
		}
		$nfc .= "\n";
		$nfc .= $fc;
		file_put_contents('/UPLOADERLOG.txt', $nfc);
		*/
		
		return new ModelAndView($model, 'service.json');
	}

	private function doDelete(array $delete) {
		foreach($delete as $path) {
			$path = rawurldecode($path);
			$subject = $this->fileManager->getNode($path);
			if($subject) {
				$subject->delete();
			}
		}
	}
	
	protected final function selectRootNode(IHttpRequest $request) {
		$node = $request->getParameter(self::FOLDER_TREE_ROOT_FOLDER_KEY);
		if(!($node instanceof Bee_Filesystem_INode)) {
			$node = $this->fileManager->getNode();
		}
		return $node;
	}
	
	

	
	
	
	
	
	
	
	
	
	
	
	
	
	/*********************************************************************************************************************************************************************************
	 * 
	 * DEPRECATED
	 * 
	 ********************************************************************************************************************************************************************************/
	
	/**
	 * Enter description here...
	 *
	 * @param IHttpRequest $request
	 */
	public function defaultMethod(IHttpRequest $request, array $mergeModel=array()) {
		throw new Exception('Method deprecated: Admin_Files_HandlerDelegate::defaultMethod()');
		$model = array();
		return new ModelAndView($model, 'admin.files');
	}

	/**
	 * Enter description here...
	 *
	 * @param IHttpRequest $request
	 * @return ModelAndView
	 * 
	 * @Bee_MVC_Controller_Multiaction_RequestHandler(pathPattern = "/**\/show")
	 */
	public final function showFileManager(IHttpRequest $request) {
		throw new Exception('Method deprecated: Admin_Files_HandlerDelegate::showFileManager()');
		$model = array();
		$model[self::FOLDER_TREE_ROOT_FOLDER_KEY] = $this->selectRootNode($request);
		$model[self::FILE_LIST_CURRENT_FOLDER_KEY] = $this->selectCurrentNode($request);
		return new ModelAndView($model, 'filemanager.panel');
	}
	
	private function handleCreateFolder(Bee_Filesystem_INode $parent, $create) {
		throw new Exception('Method deprecated: Admin_Files_HandlerDelegate::handleCreateFolder()');
		$model = array();
		$requestedFolderName = $request->getParameter(self::FILE_LIST_CURRENT_FOLDER_KEY);
		$folders = array();
		try {
			$currentNode = (!is_null($requestedFolderName)) ? $this->selectCurrentNode($requestedFolderName) : $this->selectRootNode($request);
			$dirs = $currentNode->getDirectories();
			foreach($dirs as $dir) {
				$folders[] = array(
					'label' => $dir->getManager()->toClientEncoding($dir->getFilename()),
					'path' => $dir->getManager()->getRelativePath($dir)
				);
			}
		} catch (Exception $e) {
		}
		
		$model['folders'] = $folders;
		return new ModelAndView($model, 'service.json');
	}

	private function doCreate(Bee_Filesystem_INode $parent, $create) {
		throw new Exception('Method deprecated: Admin_Files_HandlerDelegate::doCreate()');
		return $parent->createDirectory($this->fileManager->toFilesystemEncoding($create));
	}

	/**
	 * Enter description here...
	 *
	 * @param IHttpRequest $request
	 * @throws Exception
	 * @return ModelAndView
	 *
	 * @Bee_MVC_Controller_Multiaction_RequestHandler(httpMethod = "GET", pathPattern = "/**\/selectFileDialog")
	 */
	public final function showSelectFilesDialog(IHttpRequest $request) {
		throw new Exception('Method deprecated: Admin_Files_HandlerDelegate::showSelectFilesDialog()');
		$model = array();
		return new ModelAndView($model, 'admin.files.selectFileDialog');
	}

	/**
	 * @param IHttpRequest $request
	 * @return Bee_Filesystem_INode|String
	 * @throws Exception
	 */
	private final function selectCurrentNodeOld(IHttpRequest $request) {
		$node = $request->getParameter(self::FILE_LIST_CURRENT_FOLDER_KEY);
		
		if(!$node) {
			// no current node previously selected, query tree state
			$treeState = $request->getParameterValues(self::FOLDER_TREE_STATE_KEY);
			$leadSelectionId = $treeState[self::FOLDER_TREE_STATE_LEAD_SELECTION_ID_KEY];
			if(is_string($leadSelectionId)) {
				$leadSelectionId = rawurldecode($leadSelectionId);
				// @todo: catch exception if invalid path...
				$node = $this->fileManager->getNode($leadSelectionId);
			} 
			if(!$node) {
				$node = $this->fileManager->getNode(); 
			}
		} else {
			if(is_string($node)) {
				echo "is string<br/>";
				echo 'base path: '.$this->fileManager->getBasePath().'<br/>';
				$path = $this->fileManager->getBasePath(). DIRECTORY_SEPARATOR .$node;
				echo "get node by full path: $path<br/>";
				$node = $this->fileManager->getNode(rawurldecode($path));
			}
		}
		
		return $node;
	}
	
	private final function relayExceptionMessage(ModelAndView $mav, Exception $ex) {
		$mav->addModelValue('errorMessage', self::$messagebundle[$ex->getMessage()]);
		return $mav;
	}
}
