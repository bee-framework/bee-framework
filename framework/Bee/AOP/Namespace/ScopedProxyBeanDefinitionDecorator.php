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
use Bee\Context\Config\BeanDefinitionHolder;

/**
 * User: mp
 * Date: Feb 17, 2010
 * Time: 6:40:27 PM
 */

class Bee_AOP_Namespace_ScopedProxyBeanDefinitionDecorator implements Bee_Context_Xml_Namespace_IBeanDefinitionDecorator {

    public function decorate(DOMNode $node, BeanDefinitionHolder $definition, Bee_Context_Xml_ParserContext $parserContext) {

        // Register the original bean definition as it will be referenced by the scoped proxy and is relevant for tooling (validation, navigation).
        $targetBeanName = Bee_AOP_Scope_ScopedProxyUtils::getTargetBeanName($definition->getBeanName());
//        $parserContext->getReaderContext()->fireComponentRegistered(new BeanComponentDefinition(definition.getBeanDefinition(), targetBeanName));
        return Bee_AOP_Scope_ScopedProxyUtils::createScopedProxy($definition, $parserContext->getRegistry());
    }
}

?>