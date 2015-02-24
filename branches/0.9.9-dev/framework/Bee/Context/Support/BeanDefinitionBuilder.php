<?php
namespace Bee\Context\Support;
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
use Bee\Beans\PropertyValue;
use Bee\Context\Config\BeanDefinition\AbstractBeanDefinition;
use Bee\Context\Config\BeanDefinition\GenericBeanDefinition;
use Bee\Context\Config\RuntimeBeanReference;

/**
 * User: mp
 * Date: Feb 17, 2010
 * Time: 11:56:39 PM
 */
class BeanDefinitionBuilder {

    /**
     * Create a new <code>BeanDefinitionBuilder</code> used to construct a {@link GenericBeanDefinition}.
     *
     * @static
     * @param string $beanClassName the class name of the bean that the definition is being created for
     * @return BeanDefinitionBuilder
     */
    public static function genericBeanDefinition($beanClassName = null) {
        $builder = new BeanDefinitionBuilder();
        $builder->beanDefinition = new GenericBeanDefinition();
        $builder->beanDefinition->setBeanClassName($beanClassName);
        return $builder;
    }

    /**
     * Create a new <code>BeanDefinitionBuilder</code> used to construct a {@link RootBeanDefinition}.
     *
     * @static
     * @param string $beanClassName the class name for the bean that the definition is being created for
     * @param string $factoryMethodName the name of the method to use to construct the bean instance
     * @return BeanDefinitionBuilder
     */
    public static function rootBeanDefinition($beanClassName, $factoryMethodName = null) {
        $builder = new BeanDefinitionBuilder();
        $builder->beanDefinition = new GenericBeanDefinition();
        $builder->beanDefinition->setBeanClassName($beanClassName);
        $builder->beanDefinition->setFactoryMethodName($factoryMethodName);
        return $builder;
    }

    /**
     * Create a new <code>BeanDefinitionBuilder</code> used to construct a {@link ChildBeanDefinition}.
     * @static
     * @param string $parentName the name of the parent bean
     * @return BeanDefinitionBuilder
     */
    public static function childBeanDefinition($parentName) {
        $builder = new BeanDefinitionBuilder();
        $builder->beanDefinition = new GenericBeanDefinition();
        $builder->beanDefinition->setParentName($parentName);
        return $builder;
    }

    /**
     * The <code>BeanDefinition</code> instance we are creating.
     * @var AbstractBeanDefinition
     */
    private $beanDefinition;

    /**
     * Our current position with respect to constructor args.
     */
    private $constructorArgIndex;

    private function __construct() {}

    /**
     * Return the current BeanDefinition object.
     * @return AbstractBeanDefinition
     */
    public function getBeanDefinition() {
        return $this->beanDefinition;
    }

    /**
     * Set the name of the parent definition of this bean definition.
     * @param string $parentName
     * @return BeanDefinitionBuilder
     */
    public function setParentName($parentName) {
        $this->beanDefinition->setParentName($parentName);
        return $this;
    }

    /**
     * Set the name of the factory method to use for this definition.
     * @param string $factoryMethod
     * @return BeanDefinitionBuilder
     */
    public function setFactoryMethod($factoryMethod) {
        $this->beanDefinition->setFactoryMethodName($factoryMethod);
        return $this;
    }

    /**
     * Add an indexed constructor arg value. The current index is tracked internally
     * and all additions are at the present point.
     *
     * @param mixed $value
     * @return BeanDefinitionBuilder
     */
    public function addConstructorArgValue($value) {
        $this->beanDefinition->addConstructorArgumentValue(new PropertyValue($this->constructorArgIndex++, $value));
        return $this;
    }

	/**
	 * Add a reference to a named bean as a constructor arg.
	 * @param array $beanNames
	 * @return BeanDefinitionBuilder
	 */
    public function addConstructorArgReference(array $beanNames) {
        return $this->addConstructorArgValue(new RuntimeBeanReference($beanNames));
    }

    /**
     * Add the supplied property value under the given name.
     * @param string $name
     * @param mixed $value
     * @return BeanDefinitionBuilder
     */
    public function addPropertyValue($name, $value) {
        $this->beanDefinition->addPropertyValue(new PropertyValue($name, $value));
        return $this;
    }

    /**
     * Add a reference to the specified bean name under the property specified.
     * @param string $name the name of the property to add the reference to
     * @param array $beanNames the name of the bean being referenced
     * @return BeanDefinitionBuilder
     */
    public function addPropertyReference($name, array $beanNames) {
        return $this->addPropertyValue($name, new RuntimeBeanReference($beanNames));
    }

    /**
     * Set the init method for this definition.
     * @param string $methodName
     * @return BeanDefinitionBuilder
     */
    public function setInitMethodName($methodName) {
        $this->beanDefinition->setInitMethodName($methodName);
        return $this;
    }

    /**
     * Set the destroy method for this definition.
     * @param string $methodName
     * @return BeanDefinitionBuilder
     */
    public function setDestroyMethodName($methodName) {
        $this->beanDefinition->setDestroyMethodName($methodName);
        return $this;
    }

    /**
     * Set the scope of this definition.
     * @param string $scope
     * @return BeanDefinitionBuilder
     */
    public function setScope($scope) {
        $this->beanDefinition->setScope($scope);
        return $this;
    }

    /**
     * Set whether or not this definition is abstract.
     * @param boolean $flag
     * @return BeanDefinitionBuilder
     */
    public function setAbstract($flag) {
        $this->beanDefinition->setAbstract($flag);
        return $this;
    }

    /**
     * Append the specified bean name to the list of beans that this definition
     * depends on.
     */
    public function addDependsOn($beanName) {
        if ($this->beanDefinition->getDependsOn() == null) {
            $this->beanDefinition->setDependsOn(array($beanName));
        }
        else {
            $this->beanDefinition->addDependsOn(array($beanName));
        }
        return $this;
    }
}