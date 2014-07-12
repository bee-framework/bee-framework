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
use Bee\Beans\PropertyValue;
use Bee\Context\Support\BeanUtils;

/**
 * User: mp
 * Date: 19.08.13
 * Time: 23:35
 */
 
class MethodArgumentsHolder implements IMethodArguments {

	/**
	 * Enter description here...
	 *
	 * @var PropertyValue[]
	 */
	private $constructorArgumentValues = array();

	/**
	 * Return the constructor argument values for this bean.
	 * <p>The returned instance can be modified during bean factory post-processing.
	 *
	 * @return PropertyValue[]
	 */
	public function getConstructorArgumentValues() {
		return $this->constructorArgumentValues;
	}

	/**
	 * Enter description here...
	 *
	 * @param PropertyValue $arg
	 * @return void
	 */
	public function addConstructorArgumentValue(PropertyValue $arg) {
		$idx = $arg->getName();
		if(!is_int($idx) || $idx < 0) {
			trigger_error("Constructor argument index is not an integer or lower than 0 : $idx", E_USER_ERROR);
		} else {
			if(array_key_exists($idx, $this->constructorArgumentValues)) {
				BeanUtils::mergePropertyValuesIfPossible($this->constructorArgumentValues[$idx], $arg);
			}
			$this->constructorArgumentValues[$idx] = $arg;
		}
	}

	public function setConstructorArgumentValues(array $args) {
		$this->constructorArgumentValues = $args;
	}

	public function addConstructorArgumentValues(array $args) {
		foreach($args as $arg) {
			$this->addConstructorArgumentValue($arg);
		}
	}
}
