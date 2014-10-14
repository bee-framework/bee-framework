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
class NoSuchBeanDefinitionException extends BeansException {
	
	const EXCEPTION_MESSAGE = 'Bean definition for bean with name %s of type %s not found.';
	
	private $name;
	private $type;


	/**
	 * Enter description here...
	 *
	 * @param String $name
	 * @param String $type
	 * @param null $message
	 * @param Exception $prev
	 * @return NoSuchBeanDefinitionException
	 */
	public function __construct($name, $type=null, $message = null, Exception $prev = null) {
		parent::__construct(is_null($message) ? sprintf(self::EXCEPTION_MESSAGE, $name, $type) : $message, 0, $prev);
		$this->name = $name;
		$this->type = $type;
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
	public function getBeanType() {
		return $this->type;
	}
}
