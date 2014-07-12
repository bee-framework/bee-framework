<?php
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

/**
 * Enter description here...
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 * @author Benjamin Hartmann
 */
interface Bee_IContext {
	
	const FACTORY_BEAN_PREFIX = '&';
	
	/**
	 * Enter description here...
	 *
	 * @param String $beanName
	 * @return Boolean
	 */
	public function containsBean($beanName);
	
	
	/**
	 * Enter description here...
	 *
	 * @throws Bee_Context_NoSuchBeanDefinitionException
	 * @throws Bee_Context_BeanNotOfRequiredTypeException
	 * @throws Bee_Context_BeansException
	 * 
	 * @param String $beanName
	 * @param String $requiredType
	 * @return Object
	 */
	public function getBean($beanName, $requiredType=null);
	
	
	/**
	 * Enter description here...
	 *
	 * @throws Bee_Context_NoSuchBeanDefinitionException
	 * 
	 * @param String $beanName
	 * @param String $type
	 * @return Boolean
	 */
	public function isTypeMatch($beanName, $type);
	
	
	/**
	 * Enter description here...
	 *
	 * @throws Bee_Context_NoSuchBeanDefinitionException
	 * 
	 * @param String $beanName
	 * @return String
	 */
	public function getType($beanName);

    /**
     * @abstract
     * @param string $beanName
     * @return bool
     */
	public function isBeanCurrentlyInCreation($beanName);

	/**
	 * Enter description here...
	 *
	 * @return Bee_IContext
	 */
	public function getParent();

    /**
     * @abstract
     * @return string
     */
    public function getIdentifier();

	/**
	 * @param $className
	 * @return mixed
	 */
	public function getBeanNamesForType($className);
}
