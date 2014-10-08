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
use Bee\Context\Config\IObjectFactory;
use Bee\Context\Config\IScope;

/**
 * Enter description here...
 *
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class SessionScope implements IScope {
	
	const SESSION_SCOPE_PREFIX = '__sessionScopeContent';

	/**
	 * @var
	 */
	private $id;

	/**
	 * @param $id
	 */
	public function __construct($id) {
		$this->id = $id;
	}

	/**
	 * @param string $beanName
	 * @param IObjectFactory $objectFactory
	 * @return mixed|Object
	 */
	public function get($beanName, IObjectFactory $objectFactory) {
		$beans =& $_SESSION[$this->id.self::SESSION_SCOPE_PREFIX];
		$scopedObject =& $beans[$beanName];
		if(is_null($scopedObject)) {
			$scopedObject =& $objectFactory->getObject();
			$beans[$beanName] =& $scopedObject;
		}
		return $scopedObject;
	}

	/**
	 * @param $beanName
	 * @return Object
	 */
	public function remove($beanName) {
		$beans =& $_SESSION[$this->id.self::SESSION_SCOPE_PREFIX];
		$bean = $beans[$beanName];
		unset($beans[$beanName]);
		return $bean;
	}

	/**
	 * @return string
	 */
	public function getConversationId() {
		return session_id();
	}
}