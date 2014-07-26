<?php
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
use Bee\Utils\ITypeDefinitions;

/**
 * Enter description here...
 *
 * @author Benjamin Hartmann
 */
final class Bee_Utils_Types {

	private static $primitves = array(ITypeDefinitions::BOOLEAN, ITypeDefinitions::INTEGER, ITypeDefinitions::DOUBLE, ITypeDefinitions::STRING);

	/**
	 * Enter description here...
	 *
	 * @param mixed|\ReflectionClass|string $actualClassOrClassName
	 * @param string $targetClassName
	 * @return bool
	 */
	public static function isAssignable($actualClassOrClassName, $targetClassName) {
		if ($targetClassName == ITypeDefinitions::OBJECT_TYPE) {
			return is_object($actualClassOrClassName) ||  class_exists($actualClassOrClassName) ||  interface_exists($actualClassOrClassName);
		}
		if (is_string($actualClassOrClassName) && class_exists($actualClassOrClassName)) {
			// @todo: check php warnings
			return $actualClassOrClassName == $targetClassName || is_subclass_of($actualClassOrClassName, $targetClassName) || self::implementsInterface($actualClassOrClassName, $targetClassName);
		}
		if ($actualClassOrClassName instanceof ReflectionClass) {
			return $actualClassOrClassName->isSubclassOf($targetClassName) || in_array($targetClassName, $actualClassOrClassName->getInterfaceNames());
		}
		return $actualClassOrClassName instanceof $targetClassName;
	}

	/**
	 * @param $className
	 * @param $interfaceName
	 * @return bool
	 */
	public static function implementsInterface($className, $interfaceName) {
		return array_key_exists($interfaceName, class_implements($className));
	}

	/**
	 * Enter description here...
	 *
	 * @param string $typeName
	 * @return boolean
	 */
	public static function isPrimitive($typeName) {
		// @todo: isn't that actually the same as is_scalar() ???
		return in_array($typeName, self::$primitves);
	}

	/**
	 * Enter description here...
	 *
	 * @param mixed $instance
	 * @return String
	 */
	public static function getType($instance) {
		$type = gettype($instance);
		if ($type == ITypeDefinitions::OBJECT_TYPE) {
			$type = get_class($instance);
		}
		return $type;
	}
}
