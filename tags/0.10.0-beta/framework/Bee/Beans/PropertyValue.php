<?php
namespace Bee\Beans;
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
 * Enter description here...
 *
 * @author Benjamin Hartmann
 * @author Michael Plomer (michael.plomer@iter8.de)
 */
class PropertyValue {
	
	/**
	 * Enter description here...
	 *
	 * @var string
	 */
	private $name;
	
	/**
	 * Enter description here...
	 *
	 * @var mixed
	 */
	private $value;
	
	public function __construct($name, $value = null){
		$this->name = $name;
		$this->value = $value;
	}

	/**
	 * Enter description here...
	 *
	 * @return String
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Enter description here...
	 *
	 * @param String $name
	 * @return void
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * Enter description here...
	 *
	 * @return mixed
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * Enter description here...
	 *
	 * @param mixed $value
	 * @return void
	 */
	public function setValue($value) {
		$this->value = $value; 
	}
}