<?php
namespace Bee\Exceptions;
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
 * User: mp
 * Date: 30.07.13
 * Time: 17:21
 *
 * todo: move this to Bee Framework
 */
 
class ProcExecException extends \Bee_Exceptions_Base {

	private $returnValue;

	private $stdOut;

	private $stdErr;

	function __construct($message, $returnValue, $stdOut, $stdErr) {
		parent::__construct($message);
		$this->returnValue = $returnValue;
		$this->stdOut = $stdOut;
		$this->stdErr = $stdErr;
	}

	/**
	 * @return mixed
	 */
	public function getReturnValue() {
		return $this->returnValue;
	}

	/**
	 * @return mixed
	 */
	public function getStdErr() {
		return $this->stdErr;
	}

	/**
	 * @return mixed
	 */
	public function getStdOut() {
		return $this->stdOut;
	}
}
