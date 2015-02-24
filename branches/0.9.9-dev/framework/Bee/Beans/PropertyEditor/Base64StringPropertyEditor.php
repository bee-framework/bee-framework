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

/**
 * Class Base64StringPropertyEditor
 * @package Bee\Beans\PropertyEditor
 */
class Base64StringPropertyEditor implements IPropertyEditor {

	/**
	 * Enter description here...
	 *
	 * @param mixed $value
	 * @return String
	 */
	public function toString($value) {
		return base64_encode($value);
	}

	/**
	 * Enter description here...
	 *
	 * @param String $value
	 * @return mixed
	 */
	public function fromString($value) {
		return base64_decode($value);
	}
}