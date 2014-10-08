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
use Bee\Context\BeansException;

/**
 * User: mp
 * Date: Feb 17, 2010
 * Time: 6:14:37 PM
 */
interface IInstantiationAwareBeanPostProcessor extends IBeanPostProcessor {

	/**
	 * Apply this BeanPostProcessor <i>before the target bean gets instantiated</i>.
	 * The returned bean object may be a proxy to use instead of the target bean,
	 * effectively suppressing default instantiation of the target bean.
	 * <p>If a non-null object is returned by this method, the bean creation process
	 * will be short-circuited. The only further processing applied is the
	 * {@link #postProcessAfterInitialization} callback from the configured
	 * {@link BeanPostProcessor BeanPostProcessors}.
	 * <p>This callback will only be applied to bean definitions with a bean class.
	 * In particular, it will not be applied to beans with a "factory-method".
	 * <p>Post-processors may implement the extended
	 * {@link SmartInstantiationAwareBeanPostProcessor} interface in order
	 * to predict the type of the bean object that they are going to return here.
	 * @param $className
	 * @param string $beanName the name of the bean
	 * @return mixed the bean object to expose instead of a default instance of the target bean,
	 * or <code>null</code> to proceed with default instantiation
	 */
    public function postProcessBeforeInstantiation($className, $beanName);

    /**
     * Perform operations after the bean has been instantiated, via a constructor or factory method,
     * but before Spring property population (from explicit properties or autowiring) occurs.
     * <p>This is the ideal callback for performing field injection on the given bean instance.
     * See Spring's own {@link org.springframework.beans.factory.annotation.AutowiredAnnotationBeanPostProcessor}
     * for a typical example.
     * @param mixed $bean the bean instance created, with properties not having been set yet
     * @param string $beanName the name of the bean
     * @return boolean <code>true</code> if properties should be set on the bean; <code>false</code>
     * if property population should be skipped. Normal implementations should return <code>true</code>.
     * Returning <code>false</code> will also prevent any subsequent InstantiationAwareBeanPostProcessor
     * instances being invoked on this bean instance.
     * @throws BeansException in case of errors
     */
    public function postProcessAfterInstantiation($bean, $beanName);

    /**
     * Post-process the given property values before the factory applies them
     * to the given bean. Allows for checking whether all dependencies have been
     * satisfied, for example based on a "Required" annotation on bean property setters.
     * <p>Also allows for replacing the property values to apply, typically through
     * creating a new MutablePropertyValues instance based on the original PropertyValues,
     * adding or removing specific values.
     * @param pvs the property values that the factory is about to apply (never <code>null</code>)
     * @param pds the relevant property descriptors for the target bean (with ignored
     * dependency types - which the factory handles specifically - already filtered out)
     * @param bean the bean instance created, but whose properties have not yet been set
     * @param beanName the name of the bean
     * @return the actual property values to apply to to the given bean
     * (can be the passed-in PropertyValues instance), or <code>null</code>
     * to skip property population
     * @throws BeansException in case of errors
     * @see org.springframework.beans.MutablePropertyValues
     *
     * @todo
     */
//    PropertyValues postProcessPropertyValues(
//            PropertyValues pvs, PropertyDescriptor[] pds, Object bean, String beanName)
//            throws BeansException;

}
