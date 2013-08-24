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
	/**
	 * @var \ReflectionClass
	 */
	private $reflClass;

	private $values;

	private $name;

	public function __construct() {
		$this->reflClass = new \ReflectionClass($this);
		$this->values = array_diff_key($this->reflClass->getConstants(), array('ENUM_NAME'));

		echo '<hr/>EnumType ' . get_class($this) . '<br/>';
		var_dump($this);
		echo '<hr/>';
	}

	protected function getValues() {
		return $this->values;
	}

    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $values = array_map(function($val) { return "'".$val."'"; }, $this->getValues());
        return "ENUM(".implode(", ", $values).") COMMENT '(DC2Type:".$this->name.")'";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {

        if (!in_array($value, $this->getValues())) {
            throw new \InvalidArgumentException("Invalid '".$this->name."' value.");
        }
        return $value;
    }

    public function getName()
    {
        return $this->name;
    }
}