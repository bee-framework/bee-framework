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
class Bee_Beans_PropertyEditor_Registry {

	/**
	 * @var Bee_Beans_IPropertyEditor[]
	 */
	private static $converters = array();

	/**
	 *
	 * @param String $type
	 * @param Bee_Beans_IPropertyEditor $converter
	 */
	public static function registerEditor($type, Bee_Beans_IPropertyEditor $converter) {
		self::$converters[$type] = $converter;
	}
	
	
	
	/**
	 *
	 * @param String $type
	 * @return Bee_Beans_IPropertyEditor
	 */
	public static function getEditor($type) {
		if (!self::editorExists($type)) {
			throw new Exception('PropertyConverter for type "'.$type.'" not found.');
		}
		return self::$converters[$type];
	}
	
	
	
	/**
	 * Checks if a converter for the requested type is registered.
	 *
	 * @param String $type
	 * @return Boolean
	 */
	public static function editorExists($type) {
		return array_key_exists($type, self::$converters);
	}
}

Bee_Beans_PropertyEditor_Registry::registerEditor(Bee_Utils_ITypeDefinitions::INTEGER, new Bee_Beans_PropertyEditor_Generic(FILTER_VALIDATE_INT));
Bee_Beans_PropertyEditor_Registry::registerEditor(Bee_Utils_ITypeDefinitions::INT, new Bee_Beans_PropertyEditor_Generic(FILTER_VALIDATE_INT));
Bee_Beans_PropertyEditor_Registry::registerEditor(Bee_Utils_ITypeDefinitions::DOUBLE, new Bee_Beans_PropertyEditor_Generic(FILTER_VALIDATE_FLOAT));
Bee_Beans_PropertyEditor_Registry::registerEditor(Bee_Utils_ITypeDefinitions::FLOAT, new Bee_Beans_PropertyEditor_Generic(FILTER_VALIDATE_FLOAT));

Bee_Beans_PropertyEditor_Registry::registerEditor(Bee_Utils_ITypeDefinitions::BOOLEAN, new Bee_Beans_PropertyEditor_Boolean());

Bee_Beans_PropertyEditor_Registry::registerEditor(Bee_Utils_ITypeDefinitions::URL, new Bee_Beans_PropertyEditor_Generic(FILTER_VALIDATE_URL));
Bee_Beans_PropertyEditor_Registry::registerEditor(Bee_Utils_ITypeDefinitions::EMAIL, new Bee_Beans_PropertyEditor_Generic(FILTER_VALIDATE_EMAIL));
Bee_Beans_PropertyEditor_Registry::registerEditor(Bee_Utils_ITypeDefinitions::IP, new Bee_Beans_PropertyEditor_Generic(FILTER_VALIDATE_IP));
Bee_Beans_PropertyEditor_Registry::registerEditor(Bee_Utils_ITypeDefinitions::IPv4, new Bee_Beans_PropertyEditor_Generic(FILTER_VALIDATE_IP, FILTER_FLAG_IPV4));
Bee_Beans_PropertyEditor_Registry::registerEditor(Bee_Utils_ITypeDefinitions::IPv6, new Bee_Beans_PropertyEditor_Generic(FILTER_VALIDATE_IP, FILTER_FLAG_IPV6));

Bee_Beans_PropertyEditor_Registry::registerEditor(Bee_Utils_ITypeDefinitions::UNIX_TIMESTAMP, new Bee_Beans_PropertyEditor_UnixTimestamp());

Bee_Beans_PropertyEditor_Registry::registerEditor(Bee_Utils_ITypeDefinitions::STRING, new Bee_Beans_PropertyEditor_String());