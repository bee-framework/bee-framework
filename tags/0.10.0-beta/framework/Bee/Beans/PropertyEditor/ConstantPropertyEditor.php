<?php
namespace Bee\Beans\PropertyEditor;

use Bee\Beans\IPropertyEditor;
use Bee\Utils\Constants;

/**
 * Class ConstantPropertyEditor
 * @package Bee\Beans\PropertyEditor
 */
class ConstantPropertyEditor implements IPropertyEditor {

	const TYPE_STRING = 'phpconst';

	/**
	 * Enter description here...
	 *
	 * @param mixed $value
	 * @throws \Exception
	 * @return String
	 */
	public function toString($value) {
		throw new \Exception('Not implemented: ConstantPropertyEditor can only convert fromString()');
	}

	/**
	 * Enter description here...
	 *
	 * @param String $value
	 * @return mixed
	 */
	public function fromString($value) {
		return Constants::getValueFromString($value);
	}
}