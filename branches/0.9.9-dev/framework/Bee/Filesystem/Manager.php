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
use Bee\Text\EncodingConverter;
use Bee\Utils\Strings;

/**
 * Manages access to the servers local filesystem.
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 * @author Benjamin Hartmann
 */
class Bee_Filesystem_Manager {
	
	const DEFAULT_FILESYSTEM_ENCODING = 'UTF-8';
	const DEFAULT_FILESYSTEM_UNICODE_NORMALIZATION = 'NFD'; // for mac, change this to NFC in stable for unixoid operating systems
	
	const DEFAULT_CLIENT_ENCODING = 'UTF-8';
	const DEFAULT_CLIENT_UNICODE_NORMALIZATION = 'NFC';
	
	/**
	 * Enter description here...
	 *
	 * @var EncodingConverter
	 */
	private $converter;
		
	/**
	 * Enter description here...
	 *
	 * @var finfo
	 */
	private $fileInfo;
	
	/**
	 * Enter description here...
	 *
	 * @var string
	 */
	private $basePath;
	
	/**
	 * Wether to include invisible files (i.e. those starting with .)
	 *
	 * @var boolean
	 */
	private $showInvisible = false;
	
	/**
	 * A regex that is matched against a file's mime type or false if no filtering is used
	 *
	 * @var mixed
	 */
	private $mimeFilterExpr = false;

//	private static $forbiddenPathElements = array('.', '..', '..', ':');
	private static $forbiddenPathElements = array('.', ':'); // I need ".." now!

	/**
	 * Enter description here...
	 *
	 * @var Bee_Filesystem_INode
	 */
	private $rootNode;
	
	public function __construct($basePath = '') {
		$this->converter = new EncodingConverter();
		$this->converter->setExternalEncoding(self::DEFAULT_CLIENT_ENCODING);
		$this->converter->setExternalUnicodeNormalization(self::DEFAULT_CLIENT_UNICODE_NORMALIZATION);
		$this->converter->setInternalEncoding(self::DEFAULT_FILESYSTEM_ENCODING);
		$this->converter->setInternalUnicodeNormalization(self::DEFAULT_FILESYSTEM_UNICODE_NORMALIZATION);
		$this->basePath = str_replace('/', DIRECTORY_SEPARATOR, $basePath);
		umask(0000);
	}
	
	/**
	 * Enter description here...
	 *
	 * @return EncodingConverter
	 */
	public function getConverter() {
		return $this->converter;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param EncodingConverter $converter
	 * @return void
	 */
	public function setConverter(EncodingConverter $converter) {
		$this->converter = $converter;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return string
	 */
	public final function getBasePath() {
		 return $this->basePath;
	}

	/**
	 * Enter description here...
	 *
	 * @param string $basePath
	 * @return void
	 */
	public final function setBasePath($basePath) {
        $basePath = str_replace('/', DIRECTORY_SEPARATOR, $basePath);
		if(Strings::endsWith($basePath, DIRECTORY_SEPARATOR)) {
			$basePath = substr($basePath, 0, -strlen(DIRECTORY_SEPARATOR));
		}
		if($this->basePath != $basePath) {
			$this->basePath = $basePath;
			$this->rootNode = null;
		}
	}

	/**
	 * Enter description here...
	 *
	 * @param string $path
	 * @throws Exception
	 * @return Bee_Filesystem_INode
	 */
	public final function getNode($path = '') {
		$realPath = $this->toRealPath($path);
		if($realPath == $this->basePath) {
			if(is_null($this->rootNode)) {
				$this->rootNode = new Bee_Filesystem_CachedNode($this, $realPath, false);
			}
			return $this->rootNode;
		}
		if (!file_exists($realPath)) {
			throw new Exception('The directory or file with name '.$realPath.' does not exits.');
		}
		
		return new Bee_Filesystem_CachedNode($this, $realPath, false);
	}
	
	public final function createDirectory($name, $path = '') {
		$this->getNode($path)->createDirectory($name);
	}
	
	public final function rename($pathname, $newname) {
		$this->getNode($pathname)->rename($newname);
	}
	
	public final function delete($pathname) {
		$this->getNode($pathname)->delete();
	}
	
	public final function move($subjectPath, $newParentPath) {
		$this->getNode($subjectPath)->move($newParentPath);
	}
	
	public function copy($subjectPath, $newParentPath) {
		$this->getNode($subjectPath)->copy($newParentPath);
	}

	public final function toRealPath($path) {
		$path = $this->cleanPath($path);

		if(!$this->isInBasePath($path)) {
			$path = $this->basePath . $path;
		} 
		return $path;
	}
	
	public function isInBasePath($realPath) {
		return Strings::startsWith($realPath, $this->basePath);
	}
 
	public final function cleanPath($path) {
		if(strlen($path) > 0) {
			$path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
			$path = explode(DIRECTORY_SEPARATOR, $path);
			if(count($path) > 0) {
				$path = array_diff($path, self::$forbiddenPathElements);
				$path = implode(DIRECTORY_SEPARATOR, $path);
			}
		}
		return $path; 
	}	
	
	public final function cleanName($name) {
		if(strlen($name) > 0) {
			$nameClean = str_replace('\\', DIRECTORY_SEPARATOR, $name);
			$nameClean = explode(DIRECTORY_SEPARATOR, $nameClean);
			if(count($nameClean) > 1) {
				throw new FilesystemException('Invalid name given, must not contain path delimiters', $name);
			}
		}
		return $nameClean[0]; 
	}
	
	public final function toFilesystemEncoding($path) {
		return $this->converter->decode($path);
	}

	public final function toClientEncoding($path) {
		return $this->converter->encode($path);
	}
	
	public final function getDomIdFor(Bee_Filesystem_INode $node) {
		return rawurlencode($node->getPath() . DIRECTORY_SEPARATOR . $node->getFilename());
	}
	
	public function getRelativePath(Bee_Filesystem_INode $node) {
		$nodePath = $node->getPath();
		$fullpath = $nodePath;
		if ($nodePath[strlen($nodePath)-1]!=DIRECTORY_SEPARATOR) {
			$fullpath .= DIRECTORY_SEPARATOR;  
		}
		$fullpath .= $node->getFilename();
		$resultString = str_replace($this->getBasePath().DIRECTORY_SEPARATOR, '', $fullpath);
		return $resultString;
	}

	public final function formatFileSize($size) {
		$s = array('B', 'Kb', 'MB', 'GB', 'TB', 'PB');
        $e = floor(log($size)/log(1024));
        return sprintf('%.2f '.$s[$e], ($size/pow(1024, floor($e))));
	}

	public final function filterMatches(Bee_Filesystem_INode $node) {
		if(!$this->showInvisible && Strings::startsWith($node->getFilename(), '.')) {
			return false;
		}

		if($this->mimeFilterExpr !== false && preg_match($this->mimeFilterExpr, $node->getMimeType()) > 0) {
			return true;
		}
		return false;
	}

	// @TODO: Bugs Benny: is this check OK?
	public final function isRootNode(Bee_Filesystem_INode $node) {
		if (!Strings::hasText($node->getPathName())) {
			return false;
		}
		return $node->getPathName() == $this->getNode()->getPathName();
	}
}