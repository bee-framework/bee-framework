<?php
namespace Bee\Context\Config;
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
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
interface IScope {

	/**
	 * Enter description here...
	 *
	 * @param string $beanName
	 * @param IObjectFactory $objectFactory
	 * @return Object
	 */
	public function get($beanName, IObjectFactory $objectFactory);

	/**
	 * Enter description here...
	 *
	 * @param $beanName
	 * @return Object
	 */
	public function remove($beanName);

	/**
	 * Enter description here...
	 *
	 * @return String
	 */
	public function getConversationId();

	//void registerDestructionCallback(String name, Runnable callback);
}