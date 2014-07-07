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
interface Bee_Context_Config_IBeanDefinitionRegistry {
	
	/**
	 * Enter description here...
	 *
	 * @param String $beanName
	 * @return Boolean
	 */
	public function containsBeanDefinition($beanName);
	
	/**
	 * Enter description here...
	 * 
	 * @throws Bee_Context_NoSuchBeanDefinitionException
	 *
	 * @param String $beanName
	 * @return Bee_Context_Config_IBeanDefinition
	 */
	public function getBeanDefinition($beanName);
	
	/**
	 * Enter description here...
	 *
	 * @return int
	 */
	public function getBeanDefinitionCount();
	
	/**
	 * Enter description here...
	 *
	 * @return string[]
	 */
	public function getBeanDefinitionNames();
	
	/**
	 * Enter description here...
	 * 
	 * @throws Bee_Context_BeanDefinitionStoreException
	 *
	 * @param String $beanName
	 * @param Bee_Context_Config_IBeanDefinition $beanDefinition
	 * @return void
	 */
	public function registerBeanDefinition($beanName, Bee_Context_Config_IBeanDefinition $beanDefinition);
	
	/**
	 * Enter description here...
	 * 
	 * @throws Bee_Context_NoSuchBeanDefinitionException
	 *
	 * @param String $beanName
	 * @return void
	 */
	public function removeBeanDefinition($beanName);
	
	/**
	 * Enter description here...
	 *
	 * @param String $name
	 * @param String $alias
	 * @return void
	 */
	public function registerAlias($name, $alias);
	
	/**
	 * Enter description here...
	 *
	 * @param Bee_Context_Config_IBeanPostProcessor $postProcessor
	 * @return void
	 */
//	public function addBeanPostProcessor(Bee_Context_Config_IBeanPostProcessor $postProcessor);
	
	/**
	 * Enter description here...
	 *
	 * @return int
	 */
	public function getBeanPostProcessorCount();

	/**
	 * Enter description here...
	 *
	 * @return string[]
	 */
	public function getBeanPostProcessorNames();
}
