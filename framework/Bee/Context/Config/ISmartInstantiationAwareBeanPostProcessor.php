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
 * Date: Feb 18, 2010
 * Time: 7:48:50 AM
 */

interface Bee_Context_Config_ISmartInstantiationAwareBeanPostProcessor extends Bee_Context_Config_IInstantiationAwareBeanPostProcessor {

    /**
     * Predict the type of the bean to be eventually returned from this
     * processor's {@link #postProcessBeforeInstantiation} callback.
     * @param string $beanClassName the raw class of the bean
     * @param string $beanName the name of the bean
     * @return string the type of the bean, or <code>null</code> if not predictable
     * @throws org.springframework.beans.BeansException in case of errors
     */
    function predictBeanType($beanClassName, $beanName);

    /**
     * Obtain a reference for early access to the specified bean,
     * typically for the purpose of resolving a circular reference.
     * <p>This callback gives post-processors a chance to expose a wrapper
     * early - that is, before the target bean instance is fully initialized.
     * The exposed object should be equivalent to the what
     * {@link #postProcessBeforeInitialization} / {@link #postProcessAfterInitialization}
     * would expose otherwise. Note that the object returned by this method will
     * be used as bean reference unless the post-processor returns a different
     * wrapper from said post-process callbacks. In other words: Those post-process
     * callbacks may either eventually expose the same reference or alternatively
     * return the raw bean instance from those subsequent callbacks (if the wrapper
     * for the affected bean has been built for a call to this method already,
     * it will be exposes as final bean reference by default).
     * @param mixed $bean the raw bean instance
     * @param string $beanName the name of the bean
     * @return mixed the object to expose as bean reference
     * (typically with the passed-in bean instance as default)
     * @throws org.springframework.beans.BeansException in case of errors
     */
    function getEarlyBeanReference($bean, $beanName);
}
