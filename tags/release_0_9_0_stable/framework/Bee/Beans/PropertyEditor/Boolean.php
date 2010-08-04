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
class Bee_Beans_PropertyEditor_Boolean implements Bee_Beans_IPropertyEditor {

	const VALUE_TRUE = 'true';
	const VALUE_FALSE = 'false';



	/**
	 * Enter description here...
	 *
	 * @param Boolean $value
	 * @return String
	 */
	public function toString($value) {
		Bee_Utils_Assert::isTrue(is_bool($value));
		return $value ? self::VALUE_TRUE : self::VALUE_FALSE;
	}




	/**
	 * Enter description here...
	 *
	 * @param String $value
	 * @return Boolean
	 */
	public function fromString($value) {
		Bee_Utils_Assert::isTrue(is_string($value));
		return strtolower($value) == self::VALUE_TRUE;
	}

    public static function valueOf($value) {
        return Bee_Utils_Strings::hasText($value) && strtolower($value) != self::VALUE_FALSE;
    }
}
?>