<?php
namespace Bee\Exceptions;
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
 */
class TypeMismatchException extends Exception {
	
	const EXCEPTION_MESSAGE = 'Type mismatch! Expected type %s (actual is %s).';
	
	private $requiredType;
	private $actualType;


	/**
	 * Enter description here...
	 *
	 * @param String $requiredType
	 * @param String $actualType
	 * @param Exception $cause
	 * @return TypeMismatchException
	 */
	public function __construct($requiredType, $actualType=null, Exception $cause = null) {
		parent::__construct(sprintf(self::EXCEPTION_MESSAGE, $requiredType, $actualType), 0, $cause);
		$this->requiredType = $requiredType;
		$this->actualType = $actualType;
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