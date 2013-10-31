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
use Bee\Beans\MethodInvocation;

/**
 * Enter description here...
 *
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
interface Bee_Context_Config_IBeanDefinition extends Bee\Context\Config\IMethodArguments {
	
	const SCOPE_CACHE = 'cache';
	const SCOPE_SESSION = 'session';
	const SCOPE_REQUEST = 'request';
	const SCOPE_PROTOTYPE = 'prototype';
	
	/**
	 * Return the name of the parent definition of this bean definition, if any.
	 *
	 * @return String
	 */
	public function getParentName();
	
	/**
	 * Set the name of the parent definition of this bean definition, if any.
	 *
	 * @param String $parentName
	 * @return void
	 */
	public function setParentName($parentName);
	
	/**
	 * Return the current bean class name of this bean definition.
	 * <p>Note that this does not have to be the actual class name used at runtime, in
	 * case of a child definition overriding/inheriting the class name from its parent.
	 * Hence, do <i>not</i> consider this to be the definitive bean type at runtime but
	 * rather only use it for parsing purposes at the individual bean definition level.
	 * 
	 * @return String
	 */
	public function getBeanClassName();
	
	/**
	 * Override the bean class name of this bean definition.
	 * <p>The class name can be modified during bean factory post-processing,
	 * typically replacing the original class name with a parsed variant of it.
	 *
	 * @param String $beanClassName
	 * @return void
	 */
	public function setBeanClassName($beanClassName);
	
	/**
	 * Return the factory bean name, if any.
	 *
	 * @return String
	 */
	public function getFactoryBeanName();
	
	/**
	 * Specify the factory bean to use, if any.
	 *
	 * @param String $factoryBeanName
	 * @return void
	 */
	public function setFactoryBeanName($factoryBeanName);
	
	/**
	 * Return a factory method, if any.
	 *
	 * @return String
	 */
	public function getFactoryMethodName();
	
	/**
	 * Specify a factory method, if any. This method will be invoked with
	 * constructor arguments, or with no arguments if none are specified.
	 * The method will be invoked on the specifed factory bean, if any,
	 * or as static method on the local bean class else.
	 *
	 * @param String $factoryMethodName static factory method name,
	 * or <code>null</code> if normal constructor creation should be used
	 * @return void
	 * @see #getBeanClassName()
	 */
	public function setFactoryMethodName($factoryMethodName);
	
	/**
	 * Override the target scope of this bean, specifying a new scope name.
	 * @see #SCOPE_SINGLETON
	 * @see #SCOPE_PROTOTYPE
	 *
	 * @return String
	 */
	public function getScope();

	/**
	 * Enter description here...
	 *
	 * @param String $scope
	 * @return void
	 */
	public function setScope($scope);

	/**
	 * Return the property values to be applied to a new instance of the bean.
	 * <p>The returned instance can be modified during bean factory post-processing.
	 *
	 * @return Bee_Beans_PropertyValue[]
	 */
	public function getPropertyValues();

	/**
	 * Add a PropertyValue object, replacing any existing one
	 * for the corresponding property.
	 *
	 * @param Bee_Beans_PropertyValue $prop PropertyValue object to add
	 * @return Bee_Context_Config_IBeanDefinition this object to allow creating objects, adding multiple
	 * PropertyValues in a single statement
	 */
	public function addPropertyValue(Bee_Beans_PropertyValue $prop);

	/**
	 * @return MethodInvocation[]
	 */
	public function getMethodInvocations();

	/**
	 * @param MethodInvocation $methodInvocation
	 * @return void
	 */
	public function addMethodInvocation(MethodInvocation $methodInvocation);

	/**
	 * Return whether this bean is "abstract", that is, not meant to be instantiated.
	 * 
	 * @return boolean
	 */
	public function isAbstract();

	/**
	 * Enter description here...
	 *
	 * @return array
	 */
	public function getDependsOn();
	
	/**
	 * Enter description here...
	 *
	 * @param array $dependsOn
	 */
	public function setDependsOn(array $dependsOn);
	
	/**
	 * Enter description here...
	 *
	 * @param string $initMethodName
	 * @return void
	 */
	public function setInitMethodName($initMethodName);
	
	/**
	 * Enter description here...
	 *
	 * @return string
	 */
	public function getInitMethodName();
	
	/**
	 * Enter description here...
	 *
	 * @param booelan $enforceInitMethod
	 * @return void
	 */
	public function setEnforceInitMethod($enforceInitMethod);
	
	/**
	 * Enter description here...
	 *
	 * @return boolean
	 */
	public function isEnforceInitMethod();
	
	/**
	 * Enter description here...
	 *
	 * @param string $destroyMethodName
	 * @return void
	 */
	public function setDestroyMethodName($destroyMethodName);
	
	/**
	 * Enter description here...
	 *
	 * @return string
	 */
	public function getDestroyMethodName();
	
	/**
	 * Enter description here...
	 *
	 * @param boolean $enforceDestroyMethod
	 * @return void
	 */
	public function setEnforceDestroyMethod($enforceDestroyMethod);
	
	/**
	 * Enter description here...
	 *
	 * @return boolean
	 */
	public function isEnforceDestroyMethod();
	
	/**
	 * Enter description here...
	 *
	 * @param boolean $synthetic
	 * @return void
	 */
	public function setSynthetic($synthetic);
	
	/**
	 * Enter description here...
	 *
	 * @return boolean
	 */
	public function isSynthetic();
}