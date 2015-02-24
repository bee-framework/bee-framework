<?php
namespace Bee\Security;
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
use Bee\Utils\Assert;

/**
 * Class ConfigAttributeDefinition
 * @package Bee\Security
 */
class ConfigAttributeDefinition {

	/**
	 * 
	 * @var IConfigAttribute[]
	 */
	private $configAttributes;

	/**
	 * @param $attrib
	 */
	public function __construct($attrib) {
		if($attrib) {
			if(is_string($attrib)) {
				$this->configAttributes = array(new ConfigAttribute($attrib));
			} else if($attrib instanceof IConfigAttribute) {
				$this->configAttributes = array($attrib);
			} else if(is_array($attrib)) {
				$newList = array();
				foreach($attrib as $attr) {
					if(is_string($attr)) {
						$newList[] = new ConfigAttribute($attr);
					} else {
						Assert::isInstanceOf('Bee\Security\IConfigAttribute', $attr, 'List entries must be of type ConfigAttribute');
						$newList[] = $attr;
					}
				}
				$this->configAttributes = $newList;
			}
		} else {
			$this->configAttributes = array();
		}
	}

    /**
     * @return IConfigAttribute[]
     */
	public function getConfigAttributes() {
		return $this->configAttributes;
	}
}