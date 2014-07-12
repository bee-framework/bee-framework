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
final class Bee_Context_Config_Scope_Prototype implements IScope {

	/**
	 * @param $id
	 */
	public function __construct($id) {
		// id is not needed and thus not set
	}

	/**
	 * @param string $beanName
	 * @param IObjectFactory $objectFactory
	 * @return mixed|Object
	 */
	public function get($beanName, IObjectFactory $objectFactory) {
		return $objectFactory->getObject();
	}

	/**
	 * @param $beanName
	 * @return null|Object
	 */
	public function remove($beanName) {
		return null;
	}

	/**
	 * @return null|String
	 */
	public function getConversationId() {
		return null;
	}
}