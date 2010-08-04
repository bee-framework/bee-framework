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
class Bee_Context_InvalidPropertyException extends Bee_Context_BeansException {
	
	const EXCEPTION_MESSAGE = 'Could not access property %s on bean class %s: %s';
	
	private $name;
	
	
	
	/**
	 * Enter description here...
	 *
	 * @param String $name
	 * @return void
	 */
	public function __construct($propertyName, $beanClassName, $message=null) {
		parent::__construct(sprintf(self::EXCEPTION_MESSAGE, $propertyName, $beanClassName, $message));
		$this->name = $name;
	}
	

		
	/**
	 * Enter description here...
	 *
	 * @return String
	 */
	public function getBeanName() {
		return $this->name;
	}
}

?>