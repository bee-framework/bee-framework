<?php
namespace Bee\Persistence\Doctrine2\Types;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Class EnumType
 * @package Bee\Persistence\Doctrine2\Types
 */
abstract class EnumType extends Type
{
	private $values;

	private $name;

	protected abstract function getEnumClassName();

	public function __construct() {
		$reflClass = new \ReflectionClass($this->getEnumClassName());
		$this->values = $reflClass->getConstants();
		$this->name = 'enum_' . str_replace('\\', '_', $this->getEnumClassName());
	}

    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $values = array_map(function($val) { return "'".$val."'"; }, $this->values);
        return "ENUM(".implode(", ", $values).") COMMENT '(DC2Type:".$this->name.")'";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {

        if (!in_array($value, $this->values)) {
            throw new \InvalidArgumentException("Invalid '".$this->name."' value.");
        }
        return $value;
    }

    public function getName()
    {
        return $this->name;
    }
}