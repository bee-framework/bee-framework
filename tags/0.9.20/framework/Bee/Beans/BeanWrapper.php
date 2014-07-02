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
 */
class Bee_Beans_BeanWrapper {
	
	/**
	 * The target object
	 *
	 * @var object
	 */
	private $object;
	
	
	public function __construct($object) {
		$this->object = $object;
	}
	
	public final function setPropertyValue($name, $value) {
		call_user_func($this->findPropertyAccessor($name, 'set'), $value);
	}

	public final function getPropertyValue($name) {
		return call_user_func($this->findPropertyAccessor($name, 'get'));
	}
	
	protected function findPropertyAccessor($propertyName, $prefix) {
		$methodName = $prefix.ucfirst($propertyName);
		$method = array($this->object, $methodName);
		if(!is_callable($method)) {
			 throw new Bee_Context_InvalidPropertyException($propertyName, Bee_Utils_Types::getType($this->object), 'no such method found: '.$methodName);
		}
		return $method;
	}
	
	public final function setPropertyValueWithPropertyValue(Bee_Beans_PropertyValue $propertyValue) {
		$this->setPropertyValue($propertyValue->getName(), $propertyValue->getValue());
	}

	public final function setPropertyValues(array $propertyValues) {
		foreach ($propertyValues as $name => $propertyValue) {
			if (!is_string($propertyValue) && Bee_Utils_Types::isAssignable($propertyValue, "Bee_Beans_PropertyValue")) {
				$this->setPropertyValueWithPropertyValue($propertyValue);
			} else {
				$this->setPropertyValue($name, $propertyValue);
			}
		}
	}
}