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
 * todo: refactor this somehow. Doesn't really deserve a view class in its own right (MP)
 */
class SvgView extends InternalResourceView {
	
	const CHARSET_UTF8 = 'UTF-8';

	private $charset;

	public function __construct() {
		parent::__construct();
		$this->setCharset(self::CHARSET_UTF8);
	}
	
	public function getCharset() {
		return $this->charset;
	}
	
	public function setCharset($charset) {
		$this->charset = $charset;
	}
	
    protected function getContentTypeHeader() {
   		return 'image/svg+xml; charset='.$this->getCharset();
   	}

    /**
   	 * Render the internal resource given the specified model.
   	 * This includes setting the model as request attributes.
   	 */
   	protected function renderMergedOutputModel() {
        echo '<?xml version="1.0" standalone="no"?>';
        parent::renderMergedOutputModel();
   	}
}