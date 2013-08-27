<?php
namespace Bee\Utils;
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
use ReflectionClass;

/**
 * Class EnumBase
 * @package Bee\Utils
 */
abstract class EnumBase {

	/**
	 * @var array
	 */
	private static $valueToName = null;

	/**
	 * @var EnumBase[]
	 */
	private static $instancesByValue = null;

	/**
	 * @var EnumBase[]
	 */
	private static $instancesByOid = null;

	/**
	 * @var mixed
	 */
	private $value;

	private final function __construct($value) {
		$this->value = $value;
	}

	/**
	 * @return mixed
	 */
	public final function val() {
		return $this->value;
	}

	/**
	 * @return array
	 */
	public static function getValues() {
		static::init();
		return array_keys(self::$valueToName[get_called_class()]);
	}

	/**
	 * Check if valid instance
	 * @param EnumBase $inst
	 * @return bool
	 */
	public static function has(EnumBase $inst) {
		// no need to call init, as
		return isset(self::$instancesByOid[spl_object_hash($inst)]);
	}

	/**
	 * Retrieve singleton instance
	 *
	 * @param $value
	 * @return EnumBase
	 * @throws \UnexpectedValueException
	 */
	public static function get($value) {
		if(is_null($value)) {
			return $value;
		}
		static::init();
		$calledClass = get_called_class();
		if(!isset(self::$valueToName[$calledClass][$value])) {
			throw new \UnexpectedValueException('Invalid value "' . $value . '" for enum ' . $calledClass);
		}

		if(!isset(self::$instancesByValue[$calledClass][$value])) {
			$name = self::$valueToName[$calledClass][$value];
			$instanceClassName = class_exists($calledClass . '_' . $name, false) ? $calledClass . '_' . $name : $calledClass;
			$inst = new $instanceClassName($value);
			self::$instancesByValue[$calledClass][$value] = $inst;
			self::$instancesByOid[spl_object_hash($inst)] = $inst;
		}

		return self::$instancesByValue[$calledClass][$value];
	}

	private static function init() {
		$calledClass = get_called_class();
		if(!isset(self::$valueToName[$calledClass])) {
			$reflClass = new \ReflectionClass($calledClass);
			$constants = $reflClass->getConstants();
			$flipped = array_flip($constants);
			if(count($constants) !== count($flipped)) {
				throw new \UnexpectedValueException('Invalid enum definition ' . $reflClass->getName() .' : const values probably not unique');
			}
			self::$valueToName[$calledClass] = $flipped;
		}
	}

	private function __clone() {
	}

	private function __wakeup() {
	}
}
