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
use Bee\Beans\IPropertyEditor;
use Exception;

/**
 * User: mp
 * Date: 07.11.12
 * Time: 13:18
 */
abstract class AbstractPropertyEditor implements IPropertyEditor {

	/**
	 * Return either "true" or "false"
	 *
	 * @param Boolean $value
	 * @return String
	 */
	public function toString($value) {
		return strval($value);
	}

	protected function checkAndReturnIfNotFalse($value, $origValue) {
		if($value === false) {
			throw new Exception($this->getClassOrTypeName()." could not convert value '$origValue'");
		}
		return $value;
	}

	protected function checkAndReturnIfNotNull($value, $origValue) {
		if($value === null) {
			throw new Exception($this->getClassOrTypeName()." could not convert value '$origValue'");
		}
		return $value;
	}

	protected function getClassOrTypeName() {
		return get_class($this);
	}
}
