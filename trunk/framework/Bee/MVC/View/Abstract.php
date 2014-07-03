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
use Bee\MVC\IView;

/**
 * Enter description here...
 * 
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
abstract class Bee_MVC_View_Abstract implements IView {
	
	private $statusCode = 200;

	private $contentType;
	
	private $staticAttributes = array();
	
	/**
	 * @Return void
	 * @Param name String
	 * @Param object Object
	 */
	public final function addStaticAttribute($name, $object) {
		$this->staticAttributes[$name] = $object;		
	}

	public function setStaticAttributes(array $staticAttributes) {
		$this->staticAttributes = array_merge($this->staticAttributes, $staticAttributes);
	}
	
	public function getStaticAttributes() {
		return $this->staticAttributes;
	}
	
	public function getStatusCode() {
		return $this->statusCode;
	}

	public function setStatusCode($statusCode) {
		$this->statusCode = $statusCode;
	}

	public function setContentType($contentType) {
		$this->contentType = $contentType;
	}
	
	public function getContentType() {
		return $this->contentType;
	}

	/**
	 * Prepares the view given the specified model, merging it with static
	 * attributes and a RequestContext attribute, if necessary.
	 * Delegates to renderMergedOutputModel for the actual rendering.
	 * 
	 * Is final for a reason !!! (i.e. interaction with the static MODEL and subdispatch mechanism), so
	 * don't mess with it...
	 * 
	 * @Param Bee_MVC_Model $model 
	 * @Return void
	 * @see #renderMergedOutputModel
	 */
	public final function render(array $model = null) {
		// Consolidate static and dynamic model attributes.
		
		$oldModelValues = Bee_MVC_Model::getModelValues();
		Bee_MVC_Model::clear();		
//		Bee_MVC_Model::addValuesToModel($this->staticAttributes);
		Bee_MVC_Model::addValuesToModel($model);

		$this->prepareResponse();

		$this->outputStatusHeader();

		$this->renderMergedOutputModel();

		Bee_MVC_Model::clear();		
		Bee_MVC_Model::addValuesToModel($oldModelValues);
	}
	
	protected function outputStatusHeader() {
		
		switch($this->statusCode) {
			case 200:
				$header = '200 OK';
				break;
			case 400:
				$header = '400 Bad Request';
				break;
			case 401:
				$header = '401 Unauthorized';
				break;
			case 403:
				$header = '403 Forbidden';
				break;
			case 404:
				$header = '404 Not Found';
				break;
			case 500:
				$header = '500 Internal Server Error';
				break;
		}
		header('HTTP/1.0 '.$header, true, $this->statusCode);
	}
	
	/**
	 * Prepare the given response for rendering.
	 * <p>The default implementation applies a workaround for an IE bug
	 * when sending download content via HTTPS.
	 */
	protected function prepareResponse() {
		if(!headers_sent()) {
			header('Content-Type: '.$this->getContentTypeHeader());
			header('Vary: Accept');
			if ($this->generatesDownloadContent()) {
				header('Pragma: private');
				header('Cache-Control: private, must-revalidate');
			}			
		}
	}

	/**
	 * Return whether this view generates download content
	 * (typically binary content like PDF or Excel files).
	 * <p>The default implementation returns <code>false</code>. Subclasses are
	 * encouraged to return <code>true</code> here if they know that they are
	 * generating download content that requires temporary caching on the
	 * client side, typically via the response OutputStream.
	 * @see #prepareResponse
	 */
	protected function generatesDownloadContent() {
		return false;
	}

	protected function getContentTypeHeader() {
		return $this->getContentType();
	}

	protected abstract function renderMergedOutputModel();
}
?>