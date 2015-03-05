<?php
namespace Bee\Context\Config;
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
use Bee\Beans\PropertyEditor\PropertyEditorNotFoundException;
use Bee\Beans\PropertyEditor\PropertyEditorRegistry;
use Bee\Utils\ITypeDefinitions;
use Logger;

/**
 * Enter description here...
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 * @author Benjamin Hartmann
 */
class TypedStringValue {

	/**
	 * @var Logger logger is static (lightweight approach)
	 */
	protected static $log;

	/**
	 * @return Logger
	 */
	protected static function getLog() {
		if (!self::$log) {
			self::$log = Logger::getLogger(get_called_class());
		}
		return self::$log;
	}

	/**
	 * Enter description here...
	 *
	 * @var string
	 */
	private $stringValue;

	/**
	 * @var string
	 */
	private $targetTypeName;

	/**
	 * @var mixed
	 */
	private $convertedValue;

	/**
	 * @var bool
	 */
	private $converted = false;

	/**
	 * Enter description here...
	 *
	 * @param String $stringValue
	 * @param PropertyEditorRegistry $registry
	 * @param string $targetTypeName
	 */
	public function __construct($stringValue, PropertyEditorRegistry $registry, $targetTypeName = ITypeDefinitions::STRING) {
		$this->stringValue = $stringValue;
		$this->targetTypeName = $targetTypeName;
		try {
			$this->convertValue($registry);
		} catch (PropertyEditorNotFoundException $penfe) {
			if (self::getLog()->isTraceEnabled()) {
				self::getLog()->trace('Could not eagerly convert value "' . $this->stringValue . '" to required type "' . $this->targetTypeName . '" - resorting to lazy conversion. Conversion result will not be cached.', $penfe);
			}
		}
	}

	/**
	 * Enter description here...
	 *
	 * @param \Bee\Beans\PropertyEditor\PropertyEditorRegistry $registry
	 * @return String
	 */
	public function getValue(PropertyEditorRegistry $registry) {
		if (!$this->converted) {
			$this->convertValue($registry);
		}
		return $this->convertedValue;
	}

	/**
	 *
	 */
	private function convertValue(PropertyEditorRegistry $registry) {
		$this->convertedValue = $registry->getEditor($this->targetTypeName)->fromString($this->stringValue);
		$this->converted = true;
	}
}
