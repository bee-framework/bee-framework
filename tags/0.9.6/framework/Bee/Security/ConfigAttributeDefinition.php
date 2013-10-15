<?php
/*
 * Copyright 2008-2010 the original author or authors.
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

/**
 *
 */
class Bee_Security_ConfigAttributeDefinition {

	/**
	 * 
	 * @var Bee_Security_IConfigAttribute[]
	 */
	private $configAttributes;

	public function __construct($attrib) {
		if($attrib) {
			if(is_string($attrib)) {
				$this->configAttributes = array(new Bee_Security_ConfigAttribute($attrib));
			} else if($attrib instanceof Bee_Security_IConfigAttribute) {
				$this->configAttributes = array($attrib);
			} else if(is_array($attrib)) {
				$newList = array();
				foreach($attrib as $attr) {
					if(is_string($attr)) {
						$newList[] = new Bee_Security_ConfigAttribute($attr); 
					} else {
						Bee_Utils_Assert::isInstanceOf('Bee_Security_IConfigAttribute', $attr, 'List entries must be of type ConfigAttribute');
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
     * @return Bee_Security_IConfigAttribute[]
     */
	public function getConfigAttributes() {
		return $this->configAttributes;
	}
}
?>