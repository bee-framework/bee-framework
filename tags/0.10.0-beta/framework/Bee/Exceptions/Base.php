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
 * Base class for framework exception hierarchies. Adds support for root causes (i.e. Exceptions, as a result
 * of which this exception has been thrown).
 *
 * @deprecated base the Bee Exception hierarchy on the regular PHP exception (which has a "previous" property starting
 * with PHP 5.3.0)
 */
class Bee_Exceptions_Base extends Exception {
	
	public function __construct($message, Exception $cause = null) {
		parent::__construct($message, 0, $cause);
	}
	
	/**
	 * Enter description here...
	 *
	 * for backward-compatibility...
	 * @return Exception
	 */
	public final function getCause() {
		return $this->getPrevious();
	}
}