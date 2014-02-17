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
class Bee_Context_Config_Scope_Session implements Bee_Context_Config_IScope {
	
	const SESSION_SCOPE_PREFIX = '__sessionScopeContent';
	
	private $id;

	public function __construct($id) {
		$this->id = $id;
	}

	public function get($beanName, Bee_Context_Config_IObjectFactory $objectFactory) {
		$beans =& $_SESSION[$this->id.self::SESSION_SCOPE_PREFIX];
		$scopedObject =& $beans[$beanName];
		if(is_null($scopedObject)) {
			$scopedObject =& $objectFactory->getObject();
			$beans[$beanName] =& $scopedObject;
		}
		return $scopedObject;
	}

	public function remove($beanName) {
		$beans =& $_SESSION[$this->id.self::SESSION_SCOPE_PREFIX];
		$bean = $beans[$beanName];
		unset($beans[$beanName]);
		return $bean;
	}

	public function getConversationId() {
		return session_id();
	}
}

?>