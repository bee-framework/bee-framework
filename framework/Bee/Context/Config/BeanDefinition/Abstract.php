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
use Bee\Context\Config\MethodArgumentsHolder;

/**
 * Enter description here...
 *
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
abstract class Bee_Context_Config_BeanDefinition_Abstract extends MethodArgumentsHolder implements Bee_Context_Config_IBeanDefinition {

	/**
	 * String representation of the scope that this bean should live in.
	 *
	 * @var string
	 */
	private $scope = self::SCOPE_REQUEST;

	
	/**
	 * Class name of this bean. Used either to instantiate the class directly (via <code>new</code> operator), or as
	 * a static factory class (in case the @link{#$factoryMethodName} is set and the @link{#$factoryBeanName} is not).
	 *
	 * @var string
	 */
	private $beanClassName;

	
	/**
	 * Determines whether this bean is "abstract", i.e. not meant to be instantiated
	 * itself but rather just serving as parent for concrete child bean definitions.
	 *
	 * @var boolean
	 */
	private $abstractFlag;
		
	
	/**
	 * Enter description here...
	 *
	 * @var array
	 */
	private $dependsOn = array();
	
	/**
	 * Enter description here...
	 *
	 * @var Bee_Beans_PropertyValue[] array of PropertyValue instances
	 */
	private $propertyValues = array();

	/**
	 * @var MethodInvocation[]
	 */
	private $methodInvocations = array();
	
	/**
	 * Name of the factory bean, if this bean should be obtained by using another bean instance from the container as its factory.
	 *
	 * @var string
	 */
	private $factoryBeanName;
	
	/**
	 * Name of the factory method, if this bean should be obtained by invoking a factory method on a static class (determined by $beanClassName)
	 * or on a factory bean instance (determined by $factoryBeanName) 
	 *
	 * @var string
	 */
	private $factoryMethodName;
	
	/**
	 * Enter description here...
	 *
	 * @var string
	 */
	private $initMethodName;
	
	/**
	 * Enter description here...
	 *
	 * @var string
	 */
	private $destroyMethodName;

	/**
	 * Enter description here...
	 *
	 * @var boolean
	 */
	private $enforceInitMethod = true;

	/**
	 * Enter description here...
	 *
	 * @var boolean
	 */
	private $enforceDestroyMethod = true;
	
	/**
	 * Enter description here...
	 *
	 * @var boolean
	 */
	private $synthetic = false;

    /**
     * @var Bee_Context_Config_IBeanDefinition
     */
    private $originatingBeanDefinition;

    public function __construct(Bee_Context_Config_IBeanDefinition $original = null) {
		if(!is_null($original)) {
			$this->setParentName($original->getParentName());
			$this->setBeanClassName($original->getBeanClassName());
			$this->setFactoryBeanName($original->getFactoryBeanName());
			$this->setFactoryMethodName($original->getFactoryMethodName());
			$this->setScope($original->getScope());
			$this->setAbstract($original->isAbstract());
			$this->setConstructorArgumentValues($original->getConstructorArgumentValues());
			$this->setPropertyValues($original->getPropertyValues());
			$this->setMethodInvocations($original->getMethodInvocations());
			$this->setDependsOn($original->getDependsOn());
			$this->setInitMethodName($original->getInitMethodName());
			$this->setEnforceInitMethod($original->isEnforceInitMethod());
			$this->setDestroyMethodName($original->getDestroyMethodName());
			$this->setEnforceDestroyMethod($original->isEnforceDestroyMethod());
			$this->setSynthetic($original->isSynthetic());
		}
	}

	public function getScope() {
		return $this->scope;
	}
	
	public function setScope($scope) {
		$this->scope = $scope;
	}
	
	
	public function getBeanClassName() {
		return $this->beanClassName;
	}
	
	public function setBeanClassName($beanClassName) {
		$this->beanClassName = $beanClassName;
	}
	
	/**
	 * Set if this bean is "abstract", i.e. not meant to be instantiated itself but
	 * rather just serving as parent for concrete child bean definitions.
	 * <p>Default is "false". Specify true to tell the bean factory to not try to
	 * instantiate that particular bean in any case.
	 * 
	 * @param boolean $abstractFlag
	 * @return void
	 */
	public function setAbstract($abstractFlag) {
		$this->abstractFlag = $abstractFlag;
	}

	/**
	 * Return whether this bean is "abstract", i.e. not meant to be instantiated
	 * itself but rather just serving as parent for concrete child bean definitions.
	 * 
	 * @return boolean
	 */
	public function isAbstract() {
		return $this->abstractFlag;
	}

	public function getDependsOn() {
		return $this->dependsOn;
	}
	
	public function addDependsOn(array $dependsOn) {
		$this->setDependsOn(array_unique(array_merge($this->dependsOn, $dependsOn)));
	}
	
	public function setDependsOn(array $dependsOn) {
		$this->dependsOn = $dependsOn;
	}

	public function getPropertyValues() {
		return $this->propertyValues;
	}
	
	public function setPropertyValues(array $props) {
		$this->propertyValues = $props;
	}
	
	public function addPropertyValues(array $props) {
		foreach($props as $prop) {
			$this->addPropertyValue($prop);
		}
	}
	
	public function addPropertyValue(Bee_Beans_PropertyValue $prop) {
		$name = $prop->getName();
		if(!Bee_Utils_Strings::hasText($name)) {
			trigger_error("Property must have a name set", E_USER_ERROR);
		} else {
			if(array_key_exists($name, $this->propertyValues)) {
				Bee_Context_Support_BeanUtils::mergePropertyValuesIfPossible($this->propertyValues[$name], $prop);
			}
			$this->propertyValues[$name] = $prop;
		}
		return $this;
	}
	
	/**
	 * @return MethodInvocation[]
	 */
	public function getMethodInvocations() {
		return $this->methodInvocations;
	}

	/**
	 * @param MethodInvocation[] $methodInvocations
	 */
	public function setMethodInvocations(array $methodInvocations) {
		$this->methodInvocations = $methodInvocations;
	}

	/**
	 * @param MethodInvocation[] $methodInvocations
	 */
	public function addMethodInvocations(array $methodInvocations) {
		foreach($methodInvocations as $invocation) {
			$this->addMethodInvocation($invocation);
		}
	}

	/**
	 * @param MethodInvocation $methodInvocation
	 * @return void
	 */
	public function addMethodInvocation(MethodInvocation $methodInvocation) {
		array_push($this->methodInvocations, $methodInvocation);
	}

	public function getFactoryBeanName() {
		return $this->factoryBeanName;
	}
	
	
	public function setFactoryBeanName($factoryBeanName) {
		$this->factoryBeanName = $factoryBeanName;
	}
	
	
	public function getFactoryMethodName() {
		return $this->factoryMethodName;
	}
	
	
	public function setFactoryMethodName($factoryMethodName) {
		$this->factoryMethodName = $factoryMethodName;
	}
	
	
	public function setInitMethodName($initMethodName) {
		$this->initMethodName = $initMethodName;
	}
	
	
	public function getInitMethodName() {
		return $this->initMethodName;
	}
	
	
	/**
	 * Specify whether or not the configured init method is the default.
	 * Default value is <code>false</code>.
	 * @see #setInitMethodName
	 * 
	 * @param boolean $enforceInitMethod
	 */
	public function setEnforceInitMethod($enforceInitMethod) {
		$this->enforceInitMethod = $enforceInitMethod;
	}

	
	/**
	 * Indicate whether the configured init method is the default.
	 * @see #getInitMethodName()
	 * 
	 * @return boolean
	 */
	public function isEnforceInitMethod() {
		return $this->enforceInitMethod;
	}

	
	public function setDestroyMethodName($destroyMethodName) {
		$this->destroyMethodName = $destroyMethodName;
	}

	
	public function getDestroyMethodName() {
		return $this->destroyMethodName;
	}
	

	/**
	 * Specify whether or not the configured destroy method is the default.
	 * Default value is <code>false</code>.
	 * @see #setDestroyMethodName
	 * 
	 * @param boolean $enforceDestroyMethod
	 * @return void
	 */
	public function setEnforceDestroyMethod($enforceDestroyMethod) {
		$this->enforceDestroyMethod = $enforceDestroyMethod;
	}

	/**
	 * Indicate whether the configured destroy method is the default.
	 * @see #getDestroyMethodName
	 * 
	 * @return boolean
	 */
	public function isEnforceDestroyMethod() {
		return $this->enforceDestroyMethod;
	}
	
	public function setSynthetic($synthetic) {
		$this->synthetic = $synthetic;	
	}
	
	public function isSynthetic() {
		return $this->synthetic;
	}
	
    /**
     * Gets the OriginatingBeanDefinition
     *
     * @return Bee_Context_Config_IBeanDefinition $originatingBeanDefinition
     */
    public function getOriginatingBeanDefinition() {
        return $this->originatingBeanDefinition;
    }

    /**
     * Sets the OriginatingBeanDefinition
     *
     * @param $originatingBeanDefinition Bee_Context_Config_IBeanDefinition
     * @return void
     */
    public function setOriginatingBeanDefinition(Bee_Context_Config_IBeanDefinition $originatingBeanDefinition) {
        $this->originatingBeanDefinition = $originatingBeanDefinition;
    }

	public function overrideFrom(Bee_Context_Config_IBeanDefinition $other) {
		if (!is_null($other->getBeanClassName())) {
			$this->setBeanClassName($other->getBeanClassName());
		}
		if (!is_null($other->getFactoryBeanName())) {
			$this->setFactoryBeanName($other->getFactoryBeanName());
		}
		if (!is_null($other->getFactoryMethodName())) {
			$this->setFactoryMethodName($other->getFactoryMethodName());
		}
		$this->setScope($other->getScope());
		$this->setAbstract($other->isAbstract());
		$this->addConstructorArgumentValues($other->getConstructorArgumentValues());
		$this->addPropertyValues($other->getPropertyValues());
		$this->addMethodInvocations($other->getMethodInvocations());

		$this->addDependsOn($other->getDependsOn());
		if(!is_null($other->getInitMethodName())) {
			$this->setInitMethodName($other->getInitMethodName());
			$this->setEnforceInitMethod($other->isEnforceInitMethod());
		}
		
		if(!is_null($other->getDestroyMethodName())) {
			$this->setDestroyMethodName($other->getDestroyMethodName());
			$this->setEnforceDestroyMethod($other->isEnforceDestroyMethod());
		}
		
		$this->setSynthetic($other->isSynthetic());
		
	}
}