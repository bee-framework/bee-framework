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
use Bee\MVC\Model;

/**
 * Uses a PHP-includable resource (usually a local PHP file) to render the view output. Standard view for web pages.  
 * 
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class InternalResourceView extends AbstractView {

	const CONTENT_TYPE_HTML = 'text/html';
	
	public function __construct() {
		$this->setContentType(self::CONTENT_TYPE_HTML);
	}
	
	/**
	 * Render the internal resource given the specified model.
	 * This includes setting the model as request attributes.
	 */
	protected function renderMergedOutputModel() {
		// TODO : check path!!!!
		include Model::getValue('resource');
	}
}