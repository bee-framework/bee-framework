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
class Bee_Beans_PropertyEditor_Float implements Bee_Beans_IPropertyEditor {
	
	/**
	 * Enter description here...
	 *
	 * @param int $value
	 * @return String
	 */
	public function toString($value) {
		Bee_Utils_Assert::isTrue(is_float($value) || is_int($value));
		return strval($value);
	}
	
	
	
	/**
	 * Enter description here...
	 *
	 * @param String $value
	 * @return int
	 */
	public function fromString($value) {
		Bee_Utils_Assert::isTrue(is_string($value) && is_numeric($value));
		return floatval($value);
	}
}
?>