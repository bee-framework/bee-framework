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
use Exception;

/**
 * Enter description here...
 *
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class BeanCreationException extends BeansException {
	
	const EXCEPTION_MESSAGE = 'Bean with name %s could not be created: %s';
	
	private $name;

	/**
	 * Enter description here...
	 *
	 * @param String $name
	 * @param null $message
	 * @param Exception $cause
	 * @return BeanCreationException
	 */
	public function __construct($name, $message=null, Exception $cause = null) {
		parent::__construct(sprintf(self::EXCEPTION_MESSAGE, $name, $message), 0, $cause);
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