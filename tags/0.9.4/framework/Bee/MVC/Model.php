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
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class Bee_MVC_Model {
	
	const CURRENT_REQUEST_KEY = '__CURRENT_REQUEST';
	
	private static $modelValues = array();	
	
	/**
	 * Add the values from the given (associative) array to the model.
	 *
	 * @param array $values
	 */
	public static function addValuesToModel(Array $values) {
		self::$modelValues = array_merge(self::$modelValues, $values);
	}

	
	
	/**
	 * Add a single value to the model under the given key
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public static function putValue($key, $value) {
		self::$modelValues[$key] = $value;
	}

	
	
	/**
	 * Clear the model, removing all 
	 *
	 */
	public static function clear() {
		self::$modelValues = array();
	}
	
	public static function getValue($key) {
		return self::$modelValues[$key];
	}
	
	public static function getModelValues() {
		return self::$modelValues;
	}
}


/**
 * This class is a convenience for accessing 
 *
 */
class MODEL extends Bee_MVC_Model {
	
	public static function get($key) {
		return Bee_MVC_Model::getValue($key);
	}

	public static function getModel() {
		return Bee_MVC_Model::getModelValues();
	}
}
?>