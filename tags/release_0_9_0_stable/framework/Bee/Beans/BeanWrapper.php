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
		$this->invokeSetter($name, $value);
	}
	
	
	
	protected function invokeSetter($name, $value) {
		$setterMethodName = 'set'.ucfirst($name);
		$setter = array($this->object, $setterMethodName);
		if(!is_callable($setter)) {
			 throw new Bee_Context_InvalidPropertyException($name, Bee_Utils_Types::getType($this->object), 'no setter found ('.$setterMethodName.')');
		}
		call_user_func($setter, $value);
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
?>