<?php
namespace Bee\Persistence\Doctrine2\Types;
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
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Class EnumType
 * @package Bee\Persistence\Doctrine2\Types
 */
abstract class EnumType extends Type {

	const ENUM_BASE_TYPE = 'Bee\Utils\EnumBase';

	/**
	 * @var \ReflectionClass
	 */
	private $reflClass;

	/**
	 * @return string
	 */
	protected static function getEnumClassName() {
		return null;
	}

	public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform) {
		if (!$this->reflClass) self::init();
		$values = array_map(function ($val) {
			return "'" . $val . "'";
		}, call_user_func(array(static::getEnumClassName(), 'getValues')));
		return "ENUM(" . implode(", ", $values) . ") COMMENT '(DC2Type:" . $this->getName() . ")'";
	}

	public function convertToPHPValue($value, AbstractPlatform $platform) {
		if (!$this->reflClass) self::init();
		return $this->reflClass->getMethod('get')->invoke(null, $value);
	}

	public function convertToDatabaseValue($value, AbstractPlatform $platform) {
		if (!$this->reflClass) self::init();
		if(is_null($val)) {
			return $value;
		}
		if (!$this->reflClass->isInstance($value)) {
			throw new \UnexpectedValueException('Not a valid enum element for "' . static::getEnumClassName() . '": ' . $value);
		}
		// check if value is valid
		self::convertToPHPValue($value->val(), $platform);
		// return actual value
		return $value->val();
	}

	private function init() {
		$this->reflClass = new \ReflectionClass(static::getEnumClassName());
		if (!$this->reflClass->isSubclassOf(self::ENUM_BASE_TYPE)) {
			throw new \UnexpectedValueException('"' . $this->reflClass . '" is not a subclass of "' . self::ENUM_BASE_TYPE . '"');
		}
	}

	public function getName() {
		return self::getEnumName();
	}

	public static function getEnumName() {
		return 'enum'.substr(static::getEnumClassName(), strrpos(static::getEnumClassName(), '\\') + 1);
	}

	public function getMappedDatabaseTypes(AbstractPlatform $platform) {
		return array('enum');
	}
}