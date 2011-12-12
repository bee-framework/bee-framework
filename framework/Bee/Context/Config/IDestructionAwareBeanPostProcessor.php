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
 * Time: 6:14:37 PM
 */

interface Bee_Context_Config_IDestructionAwareBeanPostProcessor extends Bee_Context_Config_IBeanPostProcessor {

    /**
     * Apply this BeanPostProcessor to the given bean instance before
     * its destruction. Can invoke custom destruction callbacks.
     * <p>Like DisposableBean's <code>destroy</code> and a custom destroy method,
     * this callback just applies to singleton beans in the factory (including
     * inner beans).
     * @param mixed $bean the bean instance to be destroyed
     * @param string $beanName the name of the bean
     * @throws org.springframework.beans.BeansException in case of errors
     * @see org.springframework.beans.factory.DisposableBean
     * @see org.springframework.beans.factory.support.AbstractBeanDefinition#setDestroyMethodName
     */
    function postProcessBeforeDestruction($bean, $beanName);

}
?>