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
 * Enter description here...
 *
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class Bee_Context_Config_BeanDefinitionHolder {

	
	/**
	 * Enter description here...
	 *
	 * @var Bee_Context_Config_IBeanDefinition
	 */
	private $beanDefinition;

	
	/**
	 * Enter description here...
	 *
	 * @var String
	 */
	private $beanName; 

	
	/**
	 * Enter description here...
	 *
	 * @var array
	 */
	private $aliases;

	
	/**
	 * Enter description here...
	 *
	 * @param Bee_Context_Config_IBeanDefinition $beanDefinition
	 * @param String $beanName
	 */
	public function __construct(Bee_Context_Config_IBeanDefinition $beanDefinition, $beanName, array $aliases = null) {
		Bee_Utils_Assert::notNull($beanDefinition, 'BeanDefinition must not be null');
		Bee_Utils_Assert::hasText($beanName, 'Bean name must be present');
		$this->beanDefinition = $beanDefinition;
		$this->beanName = $beanName;
		$this->aliases = $aliases;
	}


	
	/**
	 * Enter description here...
	 *
	 * @return Bee_Context_Config_IBeanDefinition
	 */
	public function getBeanDefinition() {
		return $this->beanDefinition;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return String
	 */
    public function getBeanName() {
		return $this->beanName;
	}
	
	
	/**
	 * Enter description here...
	 *
	 * @return array
	 */
	public function getAliases() {
		return $this->aliases;
	}
}
?>