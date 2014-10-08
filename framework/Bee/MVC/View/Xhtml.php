<?php
namespace Bee\MVC\View;
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

/**
 * Uses a PHP-includable resource (usually a local PHP file) to render the view output. Standard view for web pages.  
 * 
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 *
 * @deprecated
 */
class XhtmlView extends TextView {
	
	const CONTENT_TYPE_XHTML = 'application/xhtml+xml';
	const CONTENT_TYPE_XML = 'application/xml';
	
	const DEFAULT_ROOT_TAG = 'html';
	const DEFAULT_NAMESPACE = 'http://www.w3.org/1999/xhtml';
	
	private $rootTag;
	private $defaultNamespace;
	
	public function __construct() {
		parent::__construct();  // sets default content type to 'text/html'
		
		if(stristr($_SERVER["HTTP_ACCEPT"], self::CONTENT_TYPE_XHTML)) {
			# if there's a Q value for "application/xhtml+xml" then also 
			# retrieve the Q value for "text/html"
			if(preg_match("/application\/xhtml\+xml;q=0(\.[1-9]+)/i", $_SERVER["HTTP_ACCEPT"], $matches)) {
				$xhtml_q = $matches[1];
				if(preg_match("/text\/html;q=0(\.[1-9]+)/i", $_SERVER["HTTP_ACCEPT"], $matches)) {
					$html_q = $matches[1];
					# if the Q value for XHTML is greater than or equal to that 
					# for HTML then use the "application/xhtml+xml" mimetype
					if($xhtml_q >= $html_q) {
						$this->setContentType(self::CONTENT_TYPE_XHTML);
					}
				}
				# if there was no Q value, then just use the 
				# "application/xhtml+xml" mimetype
			} else {
				$this->setContentType(self::CONTENT_TYPE_XHTML);
   			}
		}

		# special check for the W3C_Validator
		if (stristr($_SERVER["HTTP_USER_AGENT"],"W3C_Validator")) {
		   $this->setContentType(self::CONTENT_TYPE_XHTML);
		}
		
		$this->rootTag = self::DEFAULT_ROOT_TAG;
		$this->defaultNamespace = self::DEFAULT_NAMESPACE;
	}
	
	public function getRootTag() {
		return $this->rootTag;
	}
	
	public function setRootTag($rootTag) {
		$this->rootTag = $rootTag;
	}
	
	public function getDefaultNamespace() {
		return $this->defaultNamespace;
	}
	
	public function setDefaultNamespace($defaultNamespace) {
		$this->defaultNamespace = $defaultNamespace; 
	}

	protected function renderDoctypePrologue() {
		$mime = $this->getContentType(); 
		if($mime == self::CONTENT_TYPE_XHTML) {
			$charset = $this->getCharset();
			$prolog_type = "<?xml version='1.0' encoding='$charset' ?>\n<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.1//EN'\n\t'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd'>\n";
		} else if($mime == self::CONTENT_TYPE_XML) {
			$charset = $this->getCharset();
			$prolog_type = "<?xml version='1.0' encoding='$charset' ?>\n";
		} else {
   			$prolog_type = "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01//EN'\n\t'http://www.w3.org/TR/html4/strict.dtd'>\n";
		}
		echo $prolog_type;		
	}
	
	protected function renderRootElementStart() {
		if($this->getContentType() == self::CONTENT_TYPE_XHTML) {
			// @todo: additional namespaces
			echo "<$this->rootTag xmlns='$this->defaultNamespace'>\n";
		} else {
			echo "<$this->rootTag>\n";
		}
	}

	protected function renderRootElementEnd() {
		echo "\n</$this->rootTag>";
	}

	/**
	 * Render the internal resource given the specified model.
	 * This includes setting the model as request attributes.
	 */
	protected function renderMergedOutputModel() {
		$this->renderDoctypePrologue();
		$this->renderRootElementStart();
		parent::renderMergedOutputModel();
		$this->renderRootElementEnd();
	}
}