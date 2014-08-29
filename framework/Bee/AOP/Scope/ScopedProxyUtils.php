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
 * Time: 6:44:42 PM
 */

class Bee_AOP_Scope_ScopedProxyUtils {

    const TARGET_NAME_PREFIX = 'scopedTarget.';

    /**
     * Generates a scoped proxy for the supplied target bean, registering the target
     * bean with an internal name and setting 'targetBeanName' on the scoped proxy.
     * @param definition the original bean definition
     * @param registry the bean definition registry
     * @param proxyTargetClass whether to create a target class proxy
     * @return Bee_Context_Config_BeanDefinitionHolder the scoped proxy definition
     */
    public static function createScopedProxy(Bee_Context_Config_BeanDefinitionHolder $definition,
            Bee_Context_Config_IBeanDefinitionRegistry $registry) {

        $originalBeanName = $definition->getBeanName();
        $targetDefinition = $definition->getBeanDefinition();

        // Create a scoped proxy definition for the original bean name,
        // "hiding" the target bean in an internal target definition.
        $scopedProxyDefinition = new Bee_Context_Config_BeanDefinition_Generic();
        $scopedProxyDefinition->setBeanClassName('Bee_AOP_Scope_ScopedProxyFactoryBean');


        $scopedProxyDefinition->setOriginatingBeanDefinition($definition->getBeanDefinition());
//        $scopedProxyDefinition->setSource(definition.getSource());
//        $scopedProxyDefinition->setRole(BeanDefinition.ROLE_INFRASTRUCTURE);

        $targetBeanName = self::getTargetBeanName($originalBeanName);
        $scopedProxyDefinition->addPropertyValue(new Bee_Beans_PropertyValue('targetBeanName', $targetBeanName));

        // Register the target bean as separate bean in the factory.
        $registry->registerBeanDefinition($targetBeanName, $targetDefinition);

        // Return the scoped proxy definition as primary bean definition
        // (potentially an inner bean).
        return new Bee_Context_Config_BeanDefinitionHolder($scopedProxyDefinition, $originalBeanName, $definition->getAliases());
    }

    /**
     * Generates the bean name that is used within the scoped proxy to reference the target bean.
     * @param string $originalBeanName the original name of bean
     * @return the generated bean to be used to reference the target bean
     */
    public static function getTargetBeanName($originalBeanName) {
        return self::TARGET_NAME_PREFIX . $originalBeanName;
    }
}

?>