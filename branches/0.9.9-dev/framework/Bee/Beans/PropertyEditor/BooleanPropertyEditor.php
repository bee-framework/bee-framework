<?php
namespace Bee\Beans\PropertyEditor;
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
use Bee\Utils\Strings;

/**
 * Enter description here...
 *
 * @author Benjamin Hartmann
 */
class BooleanPropertyEditor extends AbstractPropertyEditor {

	const VALUE_TRUE = 'true';
	const VALUE_FALSE = 'false';

	/**
	 * Return either "true" or "false"
	 *
	 * @param Boolean $value
	 * @return String
	 */
	public function toString($value) {
		return parent::toString(!!$value);
	}

	/**
	 * Enter description here...
	 *
	 * @param String $value
	 * @return Boolean
	 */
	public function fromString($value) {
		if(is_bool($value)) {
			return $value;
		}
		return $this->checkAndReturnIfNotNull(filter_var($value === '' ? 0 : $value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE), $value);
	}

    public static function valueOf($value) {
        return Strings::hasText($value) && strtolower($value) != self::VALUE_FALSE;
    }
}
