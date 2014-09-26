<?php

namespace Bee\Context\Util;

/**
 * Class ValueFactoryBean
 * @package Bee\Context\Util
 */
class ValueFactoryBean extends AbstractFactoryBean {

	/**
	 * @var mixed
	 */
	private $sourceValue;

	protected function &createInstance() {
		return $this->sourceValue;
	}

	/**
	 * @param mixed $sourceValue
	 */
	public function setSourceValue($sourceValue) {
		$this->sourceValue = $sourceValue;
	}

	/**
	 * @return null|string
	 */
	function getObjectType() {
		return null;
	}
}