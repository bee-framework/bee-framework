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
class Bee_Context_Config_RuntimeBeanReference implements Bee_Context_Config_IBeanReference {

	/**
	 * Enter description here...
	 *
	 * @var string
	 */
    private $beanName;

    /**
     * Enter description here...
     *
     * @var boolean
     */
    private $toParent;


    /**
	 * Create a new RuntimeBeanReference to the given bean name,
	 * without explicitly marking it as reference to a bean in
	 * the parent factory.
	 * @param String $beanName name of the target bean
	 * @param Boolean $toParent whether this is an explicit reference to
	 * a bean in the parent factory
	 */
	public function __construct($beanName, $toParent = false) {
		$this->beanName = $beanName;
		$this->toParent = $toParent;
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
	 * Return whether this is an explicit reference to a bean
	 * in the parent factory.
	 * @return Boolean
	 */
	public function isToParent() {
		return $this->toParent;
	}
}
?>