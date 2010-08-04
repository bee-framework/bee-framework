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
 * Time: 4:59:01 PM
 * To change this template use File | Settings | File Templates.
 */

class Bee_Security_Namespace_GlobalMethodSecurityBeanDefinitionParser implements Bee_Context_Xml_Namespace_IBeanDefinitionParser {

    const SECURED_METHOD_DEFINITION_SOURCE_CLASS = 'Bee_Security_Annotations_SecuredMethodDefinitionSource';

    const ATT_USE_SECURED = 'secured-annotations';
    const ATT_ACCESS_MGR = "access-decision-manager-ref";

    public function parse(DOMElement $element, Bee_Context_Xml_ParserContext $parserContext) {
//        $source = $parserContext->extractSource(element);

        // The list of method metadata delegates
        $delegates = array();

        $this->registerAnnotationBasedMethodDefinitionSources($element, $parserContext, $delegates);

//        $mapBasedMethodDefinitionSource = new MapBasedMethodDefinitionSource();
//        $delegates[] = $mapBasedMethodDefinitionSource;

        // Now create a Map<String, ConfigAttribute> for each <protect-pointcut> sub-element
//        Map pointcutMap = parseProtectPointcuts(parserContext,
//                DomUtils.getChildElementsByTagName(element, Elements.PROTECT_POINTCUT));
//
//        if (pointcutMap.size() > 0) {
//            registerProtectPointcutPostProcessor(parserContext, pointcutMap, mapBasedMethodDefinitionSource, source);
//        }

        $this->registerDelegatingMethodDefinitionSource($parserContext, $delegates, $element);

        // Register the applicable AccessDecisionManager, handling the special JSR 250 voter if being used
        $accessManagerId = $element->getAttribute(self::ATT_ACCESS_MGR);

        if (!Bee_Utils_Strings::hasText($accessManagerId)) {
            Bee_Security_Config_Utils::registerDefaultAccessManagerIfNecessary($parserContext);
            $accessManagerId = Bee_Security_Config_IBeanIds::ACCESS_MANAGER;
        }

        $this->registerMethodSecurityInterceptor($parserContext, $accessManagerId, $element);

        $this->registerAdvisor($parserContext, $element);

        Bee_AOP_Namespace_Utils::registerAutoProxyCreatorIfNecessary($parserContext, $element);

        return null;
    }

    /**
     * Checks whether Secured annotations are enabled and adds the appropriate MethodDefinitionSource delegates if required.
     */
    private function registerAnnotationBasedMethodDefinitionSources(DOMElement $element, Bee_Context_Xml_ParserContext $pc, array &$delegates) {
        if ($element->getAttribute(self::ATT_USE_SECURED) == 'enabled') {
            $delegates[] = Bee_Context_Support_BeanDefinitionBuilder::rootBeanDefinition(self::SECURED_METHOD_DEFINITION_SOURCE_CLASS)->getBeanDefinition();
        }
    }

    private function registerDelegatingMethodDefinitionSource(Bee_Context_Xml_ParserContext $parserContext, array &$delegates, DOMElement $element) {
        if ($parserContext->getRegistry()->containsBeanDefinition(Bee_Security_Config_IBeanIds::DELEGATING_METHOD_DEFINITION_SOURCE)) {
            $parserContext->getReaderContext()->error("Duplicate <global-method-security> detected.", $element);
        }
        $delegatingMethodDefinitionSource = new Bee_Context_Config_BeanDefinition_Generic();
        $delegatingMethodDefinitionSource->setBeanClassName('Bee_Security_Intercept_DelegatingMethodDefinitionSource');
        $delegatingMethodDefinitionSource->addPropertyValue(new Bee_Beans_PropertyValue('methodDefinitionSources', $delegates));
        $parserContext->getRegistry()->registerBeanDefinition(Bee_Security_Config_IBeanIds::DELEGATING_METHOD_DEFINITION_SOURCE, $delegatingMethodDefinitionSource);
    }

    private function registerMethodSecurityInterceptor(Bee_Context_Xml_ParserContext $parserContext, $accessManagerId, DOMElement $element) {
        $interceptor = new Bee_Context_Config_BeanDefinition_Generic();
        $interceptor->setBeanClassName('Bee_Security_Intercept_MethodSecurityInterceptor');
        $interceptor->addPropertyValue(new Bee_Beans_PropertyValue('accessDecisionManager', new Bee_Context_Config_RuntimeBeanReference($accessManagerId)));
        $interceptor->addPropertyValue(new Bee_Beans_PropertyValue('authenticationManager', new Bee_Context_Config_RuntimeBeanReference(Bee_Security_Config_IBeanIds::AUTHENTICATION_MANAGER)));
        $interceptor->addPropertyValue(new Bee_Beans_PropertyValue('objectDefinitionSource', new Bee_Context_Config_RuntimeBeanReference(Bee_Security_Config_IBeanIds::DELEGATING_METHOD_DEFINITION_SOURCE)));
        $parserContext->getRegistry()->registerBeanDefinition(Bee_Security_Config_IBeanIds::METHOD_SECURITY_INTERCEPTOR, $interceptor);

//        $parserContext->registerComponent(new BeanComponentDefinition($interceptor, Bee_Security_Config_IBeanIds::METHOD_SECURITY_INTERCEPTOR));

        $interceptorPostProcessor = new Bee_Context_Config_BeanDefinition_Generic();
        $interceptorPostProcessor->setBeanClassName('Bee_Security_Config_MethodSecurityInterceptorPostProcessor');
        $parserContext->getRegistry()->registerBeanDefinition(Bee_Security_Config_IBeanIds::METHOD_SECURITY_INTERCEPTOR_POST_PROCESSOR, $interceptorPostProcessor);
    }

    private function registerAdvisor(Bee_Context_Xml_ParserContext $parserContext) {
        $advisor = new Bee_Context_Config_BeanDefinition_Generic();
        $advisor->setBeanClassName('Bee_Security_Intercept_MethodDefinitionSourceAdvisor');
        $advisor->addConstructorArgumentValue(new Bee_Beans_PropertyValue(0, Bee_Security_Config_IBeanIds::METHOD_SECURITY_INTERCEPTOR));
        $advisor->addConstructorArgumentValue(new Bee_Beans_PropertyValue(1, new Bee_Context_Config_RuntimeBeanReference(Bee_Security_Config_IBeanIds::DELEGATING_METHOD_DEFINITION_SOURCE)));
        $parserContext->getRegistry()->registerBeanDefinition(Bee_Security_Config_IBeanIds::METHOD_DEFINITION_SOURCE_ADVISOR, $advisor);
    }
}
