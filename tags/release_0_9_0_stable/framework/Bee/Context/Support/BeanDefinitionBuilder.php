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
 * User: mp
 * Date: Feb 17, 2010
 * Time: 11:56:39 PM
 */

class Bee_Context_Support_BeanDefinitionBuilder {

    /**
     * Create a new <code>BeanDefinitionBuilder</code> used to construct a {@link GenericBeanDefinition}.
     *
     * @static
     * @param string $beanClassName the class name of the bean that the definition is being created for
     * @return Bee_Context_Support_BeanDefinitionBuilder
     */
    public static function genericBeanDefinition($beanClassName = null) {
        $builder = new Bee_Context_Support_BeanDefinitionBuilder();
        $builder->beanDefinition = new Bee_Context_Config_BeanDefinition_Generic();
        $builder->beanDefinition->setBeanClassName($beanClassName);
        return $builder;
    }

    /**
     * Create a new <code>BeanDefinitionBuilder</code> used to construct a {@link RootBeanDefinition}.
     *
     * @static
     * @param string $beanClassName the class name for the bean that the definition is being created for
     * @param string $factoryMethodName the name of the method to use to construct the bean instance
     * @return Bee_Context_Support_BeanDefinitionBuilder
     */
    public static function rootBeanDefinition($beanClassName, $factoryMethodName = null) {
        $builder = new Bee_Context_Support_BeanDefinitionBuilder();
        $builder->beanDefinition = new Bee_Context_Config_BeanDefinition_Generic();
        $builder->beanDefinition->setBeanClassName($beanClassName);
        $builder->beanDefinition->setFactoryMethodName($factoryMethodName);
        return $builder;
    }

    /**
     * Create a new <code>BeanDefinitionBuilder</code> used to construct a {@link ChildBeanDefinition}.
     * @static
     * @param string $parentName the name of the parent bean
     * @return Bee_Context_Support_BeanDefinitionBuilder
     */
    public static function childBeanDefinition($parentName) {
        $builder = new Bee_Context_Support_BeanDefinitionBuilder();
        $builder->beanDefinition = new Bee_Context_Config_BeanDefinition_Generic();
        $builder->beanDefinition->setParentName($parentName);
        return $builder;
    }

    /**
     * The <code>BeanDefinition</code> instance we are creating.
     * @var Bee_Context_Config_BeanDefinition_Abstract
     */
    private $beanDefinition;

    /**
     * Our current position with respect to constructor args.
     */
    private $constructorArgIndex;

    private function __construct() {}

    /**
     * Return the current BeanDefinition object.
     * @return Bee_Context_Config_BeanDefinition_Abstract
     */
    public function getBeanDefinition() {
        return $this->beanDefinition;
    }

    /**
     * Set the name of the parent definition of this bean definition.
     * @param string $parentName
     * @return Bee_Context_Support_BeanDefinitionBuilder
     */
    public function setParentName($parentName) {
        $this->beanDefinition->setParentName($parentName);
        return $this;
    }

    /**
     * Set the name of the factory method to use for this definition.
     * @param string $factoryMethod
     * @return Bee_Context_Support_BeanDefinitionBuilder
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
     * @return Bee_Context_Support_BeanDefinitionBuilder
     */
    public function addConstructorArgValue($value) {
        $this->beanDefinition->addConstructorArgumentValue(new Bee_Beans_PropertyValue($this->constructorArgIndex++, $value));
        return $this;
    }

    /**
     * Add a reference to a named bean as a constructor arg.
     * @param string $beanName
     * @return Bee_Context_Support_BeanDefinitionBuilder
     */
    public function addConstructorArgReference($beanName) {
        return $this->addConstructorArgValue(new Bee_Context_Config_RuntimeBeanReference($beanName));
    }

    /**
     * Add the supplied property value under the given name.
     * @param string $name
     * @param mixed $value
     * @return Bee_Context_Support_BeanDefinitionBuilder
     */
    public function addPropertyValue($name, $value) {
        $this->beanDefinition->addPropertyValue(new Bee_Beans_PropertyValue($name, $value));
        return $this;
    }

    /**
     * Add a reference to the specified bean name under the property specified.
     * @param string $name the name of the property to add the reference to
     * @param string $beanName the name of the bean being referenced
     * @return Bee_Context_Support_BeanDefinitionBuilder
     */
    public function addPropertyReference($name, $beanName) {
        return $this->addPropertyValue($name, new Bee_Context_Config_RuntimeBeanReference($beanName));
    }

    /**
     * Set the init method for this definition.
     * @param string $methodName
     * @return Bee_Context_Support_BeanDefinitionBuilder
     */
    public function setInitMethodName($methodName) {
        $this->beanDefinition->setInitMethodName($methodName);
        return $this;
    }

    /**
     * Set the destroy method for this definition.
     * @param string $methodName
     * @return Bee_Context_Support_BeanDefinitionBuilder
     */
    public function setDestroyMethodName($methodName) {
        $this->beanDefinition->setDestroyMethodName($methodName);
        return $this;
    }

    /**
     * Set the scope of this definition.
     * @param string $scope
     * @return Bee_Context_Support_BeanDefinitionBuilder
     */
    public function setScope($scope) {
        $this->beanDefinition->setScope($scope);
        return $this;
    }

    /**
     * Set whether or not this definition is abstract.
     * @param boolean $flag
     * @return Bee_Context_Support_BeanDefinitionBuilder
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
