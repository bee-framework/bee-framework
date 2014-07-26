<?php

namespace Test;
use Bee\Beans\IPropertyEditor;


/**
 * Class MiscClassPropertyEditor
 * @package Test
 */
class MiscClassPropertyEditor implements IPropertyEditor {

	/**
	 * Enter description here...
	 *
	 * @param MiscClass $value
	 * @return String
	 */
	public function toString($value) {
		return $value->getId();
	}

	/**
	 * Enter description here...
	 *
	 * @param String $value
	 * @return mixed
	 */
	public function fromString($value) {
		return new MiscClass(intval($value));
	}
}