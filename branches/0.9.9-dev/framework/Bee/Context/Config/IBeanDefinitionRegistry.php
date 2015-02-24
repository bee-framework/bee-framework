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
use Bee\Context\BeanDefinitionStoreException;
use Bee\Context\NoSuchBeanDefinitionException;

/**
 * Enter description here...
 *
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
interface IBeanDefinitionRegistry {
	
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
	 * @throws NoSuchBeanDefinitionException
	 *
	 * @param String $beanName
	 * @return IBeanDefinition
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
	 * @throws BeanDefinitionStoreException
	 *
	 * @param String $beanName
	 * @param IBeanDefinition $beanDefinition
	 * @return void
	 */
	public function registerBeanDefinition($beanName, IBeanDefinition $beanDefinition);
	
	/**
	 * Enter description here...
	 * 
	 * @throws NoSuchBeanDefinitionException
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
	 * @param IBeanPostProcessor $postProcessor
	 * @return void
	 */
//	public function addBeanPostProcessor(IBeanPostProcessor $postProcessor);
	
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
