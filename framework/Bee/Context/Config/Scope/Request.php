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
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class Bee_Context_Config_Scope_Request implements Bee_Context_Config_IScope {
	
	private $beans;

	public function __construct($id) {
		// id is not needed and thus not set
	}

	public function get($beanName, Bee_Context_Config_IObjectFactory $objectFactory) {
		$scopedObject =& $this->beans[$beanName];
		if(is_null($scopedObject)) {
			$scopedObject = $objectFactory->getObject();
			$this->beans[$beanName] = $scopedObject;
		}
		return $scopedObject;
	}
	
	public function remove($beanName) {
		$bean = $this->beans[$beanName];
		unset($this->beans[$beanName]);
		return $bean;
	}

	public function getConversationId() {
		return null;
	}

	/**
	 * Enter description here...
	 *
	 * @return Array
	 */
	public function __sleep() {
		return Array();
	}
}

?>