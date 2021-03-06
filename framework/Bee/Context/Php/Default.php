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
use Bee\Context\AbstractContext;

/**
 * Enter description here...
 *
 * @deprecated
 * 
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class Bee_Context_Php_Default extends AbstractContext {
	
	/**
	 * Enter description here...
	 *
	 * @var String
	 */
	private $defSourceClassName;
	
	public function __construct($defSourceClassName, $callInitMethod=true) {
		parent::__construct($defSourceClassName, false);
		$this->defSourceClassName = $defSourceClassName;
		if ($callInitMethod) {
			$this->init();
		}
	}
	
	protected function loadBeanDefinitions() {
		$src = new $this->defSourceClassName();
		$defs = $src->getBeanDefinitions();
		foreach ($defs as $name => $def) {
			$this->registerBeanDefinition($name, $def);
		}
	}
}