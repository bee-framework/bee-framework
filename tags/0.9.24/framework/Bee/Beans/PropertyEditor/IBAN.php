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
 * User: mp
 * Date: 29.04.13
 * Time: 22:08
 */
 
class Bee_Beans_PropertyEditor_IBAN extends Bee_Beans_PropertyEditor_Abstract {

	const IBAN_CHECK_REGEX = '/^([A-Z]{2}\d{2}) ?(\d{4} ?){1,15}$/';

	public function __construct() {
		throw new Exception("not implemented");
	}

	/**
	 * Enter description here...
	 *
	 * @param String $value
	 * @return String
	 */
	public function fromString($value) {
		throw new Exception("not implemented");
	}
}
