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

/**
 * Enter description here...
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class Bee_Context_Xml_DocumentDefaultsDefinition {
	
	/**
	 * Enter description here...
	 *
	 * @var bool
	 */
	private $merge;
	
	/**
	 * Enter description here...
	 *
	 * @var String
	 */
	private $initMethod;
	
	/**
	 * Enter description here...
	 *
	 * @var String
	 */
	private $destroyMethod;
	
	/**
	 * Enter description here...
	 *
	 * @param bool $merge
	 * @return void
	 */
	public function setMerge($merge) {
		$this->merge = $merge;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return bool
	 */
	public function getMerge() {
		return $this->merge;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param String $initMethod
	 * @return void
	 */
	public function setInitMethod($initMethod) {
		$this->initMethod = $initMethod;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return String
	 */
	public function getInitMethod() {
		return $this->initMethod;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param String $destroyMethod
	 */
	public function setDestroyMethod($destroyMethod) {
		$this->destroyMethod = $destroyMethod;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return String
	 */
	public function getDestroyMethod() {
		return $this->destroyMethod;
	}
}
?>