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
 * Date: Feb 19, 2010
 * Time: 5:41:23 PM
 */

class Bee_AOP_Config_Utils {

    const AUTO_PROXY_CREATOR_BEAN_NAME = 'org.beeframework.aop.config.internalAutoProxyCreator';

    /**
     * @static
     * @param Bee_Context_Config_IBeanDefinitionRegistry $registry
     * @return Bee_Context_Config_BeanDefinition_Generic
     */
    public static function registerAutoProxyCreatorIfNecessary(Bee_Context_Config_IBeanDefinitionRegistry $registry) {
        return self::registerOrEscalateApcAsRequired('Bee_AOP_Framework_AutoProxyCreator', $registry);
//        return self::registerOrEscalateApcAsRequired('InfrastructureAdvisorAutoProxyCreator.class', $registry, $source);
    }

    /**
     * @static
     * @access private static
     * @param  $className
     * @param Bee_Context_Config_IBeanDefinitionRegistry $registry
     * @return Bee_Context_Config_BeanDefinition_Generic
     */
    private static function registerOrEscalateApcAsRequired($className, Bee_Context_Config_IBeanDefinitionRegistry $registry) {
        if ($registry->containsBeanDefinition(self::AUTO_PROXY_CREATOR_BEAN_NAME)) {
            // todo
//            $apcDefinition = $registry->getBeanDefinition(self::AUTO_PROXY_CREATOR_BEAN_NAME);
//            if (!cls.getName().equals(apcDefinition.getBeanClassName())) {
//                int currentPriority = findPriorityForClass(apcDefinition.getBeanClassName());
//                int requiredPriority = findPriorityForClass(cls.getName());
//                if (currentPriority < requiredPriority) {
//                    apcDefinition.setBeanClassName(cls.getName());
//                }
//            }
            return null;
        }

        $beanDefinition = new Bee_Context_Config_BeanDefinition_Generic();
        $beanDefinition->setBeanClassName($className);
//        $beanDefinition->addPropertyValue(new Bee_Beans_PropertyValue('order', Ordered.HIGHEST_PRECEDENCE)); // todo: implement bean ordering
        $registry->registerBeanDefinition(self::AUTO_PROXY_CREATOR_BEAN_NAME, $beanDefinition);
        return $beanDefinition;
    }
}

?>