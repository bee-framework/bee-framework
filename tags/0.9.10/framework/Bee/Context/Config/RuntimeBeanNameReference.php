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
class Bee_Context_Config_RuntimeBeanNameReference implements Bee_Context_Config_IBeanReference {
	
	/**
	 * Enter description here...
	 *
	 * @var array
	 */
    private $beanNames;


    /**
	 * Create a new RuntimeBeanReference to the given bean name,
	 * without explicitly marking it as reference to a bean in
	 * the parent factory.
	 * @param array $beanNames name of the target bean
	 */
	public function __construct(array $beanNames) {
		$this->beanNames = $beanNames;
	}


	
	/**
	 * Enter description here...
	 *
	 * @return String
	 */
    public function getBeanNames() {
		return $this->beanNames;
	}
}
?>