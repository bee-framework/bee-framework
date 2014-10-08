<?php
namespace Bee\Context;
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
class BeanNotOfRequiredTypeException extends BeansException {
	
	const EXCEPTION_MESSAGE = 'Bean with name %s has type %s (should be %s).';

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $requiredType;

	/**
	 * @var string
	 */
	private $actualType;

	/**
	 * Enter description here...
	 *
	 * @param String $name
	 * @param String $requiredType
	 * @param String $actualType
	 * @return BeanNotOfRequiredTypeException
	 */
	public function __construct($name, $requiredType, $actualType=null) {
		parent::__construct(sprintf(self::EXCEPTION_MESSAGE, $name, $actualType, $requiredType));
		$this->name = $name;
		$this->requiredType = $requiredType;
		$this->actualType = $actualType;
	}

	/**
	 * Enter description here...
	 *
	 * @return String
	 */
	public function getBeanName() {
		return $this->name;
	}

	/**
	 * Enter description here...
	 *
	 * @return String
	 */
	public function getRequiredType() {
		return $this->requiredType;
	}

	/**
	 * Enter description here...
	 *
	 * @return String
	 */
	public function getActualType() {
		return $this->actualType;
	}
}