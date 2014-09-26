<?php

namespace Bee\Beans\PropertyEditor;
use Bee\Beans\IPropertyEditor;
use Exception;


/**
 * Class UrlParamPropertyEditor
 * @package Bee\Beans\PropertyEditor
 */
class UrlParamPropertyEditor implements IPropertyEditor {

	const TYPE_STRING = 'urlparams';

	/**
	 * Enter description here...
	 *
	 * @param mixed $value
	 * @throws Exception
	 * @return String
	 */
	public function toString($value) {
		throw new Exception('not implemented');
	}

	/**
	 * Enter description here...
	 *
	 * @param String $value
	 * @return mixed
	 */
	public function fromString($value) {
		$result = array();
		parse_str($value, $result);
		return $result;
	}
}