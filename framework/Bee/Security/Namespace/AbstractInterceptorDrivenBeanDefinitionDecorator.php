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
 * Created by IntelliJ IDEA.
 * User: mp
 * Date: Feb 19, 2010
 * Time: 4:42:25 PM
 * To change this template use File | Settings | File Templates.
 */
// todo: work in progress...
//class Bee_Security_Namespace_AbstractInterceptorDrivenBeanDefinitionDecorator implements Bee_Context_Xml_Namespace_IBeanDefinitionDecorator {
//
//    public final function decorate(DOMNode $node, Bee_Context_Config_BeanDefinitionHolder $definitionHolder, Bee_Context_Xml_ParserContext $parserContext) {
//        $registry = $parserContext->getRegistry();
//
//        // get the root bean name - will be the name of the generated proxy factory bean
//        $existingBeanName = $definitionHolder->getBeanName();
//        $existingDefinition = $definitionHolder->getBeanDefinition();
//
//        // delegate to subclass for interceptor def
//        $interceptorDefinition = $this->createInterceptorDefinition($node);
//
//        // generate name and register the interceptor
//        $interceptorName = $existingBeanName . '.' . $this->getInterceptorNameSuffix($interceptorDefinition);
//        Bee_Context_Support_BeanDefinitionReaderUtils::registerBeanDefinition(new Bee_Context_Config_BeanDefinitionHolder($interceptorDefinition, $interceptorName), $registry);
//
//        $result = $definitionHolder;
//
//        if (!$this->isProxyFactoryBeanDefinition($existingDefinition)) {
//
//            // create the proxy definitionHolder
//            $proxyDefinition = new RootBeanDefinition();
//            // create proxy factory bean definitionHolder
//            $proxyDefinition->setBeanClass(ProxyFactoryBean.class);
//
//            // set up property values
//            MutablePropertyValues mpvs = new MutablePropertyValues();
//            proxyDefinition.setPropertyValues(mpvs);
//
//            // set the target
//            mpvs.addPropertyValue("target", existingDefinition);
//
//            // create the interceptor names list
//            ManagedList interceptorList = new ManagedList();
//            mpvs.addPropertyValue("interceptorNames", interceptorList);
//
//            result = new BeanDefinitionHolder(proxyDefinition, existingBeanName);
//        }
//
//        addInterceptorNameToList(interceptorName, result.getBeanDefinition());
//
//        return result;
//
//    }
//
//    private void addInterceptorNameToList(String interceptorName, BeanDefinition beanDefinition) {
//        List list = (List) beanDefinition.getPropertyValues().getPropertyValue("interceptorNames").getValue();
//        list.add(interceptorName);
//    }
//
//    private boolean isProxyFactoryBeanDefinition(BeanDefinition existingDefinition) {
//        return existingDefinition.getBeanClassName().equals(ProxyFactoryBean.class.getName());
//    }
//
//    protected String getInterceptorNameSuffix(BeanDefinition interceptorDefinition) {
//        return StringUtils.uncapitalize(ClassUtils.getShortName(interceptorDefinition.getBeanClassName()));
//    }
//
//    /**
//     * Subclasses should implement this method to return the <code>BeanDefinition</code>
//     * for the interceptor they wish to apply to the bean being decorated.
//     */
//    protected abstract BeanDefinition createInterceptorDefinition(Node node);
//}
?>