<?php
namespace Bee\Persistence\Doctrine2\Types;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Class EnumType
 * @package Bee\Persistence\Doctrine2\Types
 */
abstract class EnumType extends Type {

	const ENUM_BASE_TYPE = 'Bee\Utils\EnumBase';

	/**
	 * @var array
	 */
	private $values;

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

	public function __construct() {
		$this->reflClass = new \ReflectionClass(static::getEnumClassName());
		if (!$this->reflClass->isSubclassOf(self::ENUM_BASE_TYPE)) {
			throw new \UnexpectedValueException('"' . $this->reflClass . '" is not a subclass of "' . self::ENUM_BASE_TYPE . '"');
		}
	}

	public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform) {
		$values = array_map(function ($val) {
			return "'" . $val . "'";
		}, $this->reflClass->getMethod('getValues')->invoke(null));
		return "ENUM(" . implode(", ", $values) . ") COMMENT '(DC2Type:" . $this->getName() . ")'";
	}

	public function convertToPHPValue($value, AbstractPlatform $platform) {
		return $this->reflClass->getMethod('get')->invoke(null, $value);
	}

	public function convertToDatabaseValue($value, AbstractPlatform $platform) {
		if (!$this->reflClass->isInstance($value)) {
			throw new \UnexpectedValueException('Not a valid enum element for "' . self::ENUM_BASE_TYPE . '": ' . $value);
		}
		// check if value is valid
		self::convertToPHPValue($value->val(), $platform);
		// return actual value
		return $value->val();
	}

	public function getName() {
		return self::getEnumName();
	}

	public static function getEnumName() {
		return 'enum_' . str_replace('\\', '', static::getEnumClassName());
	}
}