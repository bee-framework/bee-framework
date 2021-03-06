<?php
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
use Bee\Context\Config\BeanDefinition\GenericBeanDefinition;
use Bee\Context\Support\BeanDefinitionBuilder;
use Bee\Context\Xml\ParserContext;

/**
 * User: mp
 * Date: Feb 19, 2010
 * Time: 5:30:49 PM
 */

class Bee_Security_Config_Utils {

    public static function registerDefaultAccessManagerIfNecessary(ParserContext $parserContext) {

        if (!$parserContext->getRegistry()->containsBeanDefinition(Bee_Security_Config_IBeanIds::ACCESS_MANAGER)) {

            $roleVoter = new GenericBeanDefinition();
            $roleVoter->setBeanClassName('Bee\Security\Vote\RoleVoter');

//            $authenticatedVoter = new GenericBeanDefinition();
//            $authenticatedVoter->setBeanClassName('Bee\Security\Vote\AuthenticatedVoter'); // todo: implement...

            $defaultVoters = array(
                $roleVoter/*,
                $authenticatedVoter*/
            );

            $accessMgrBuilder = BeanDefinitionBuilder::rootBeanDefinition('Bee\Security\Vote\AffirmativeBased');
            $accessMgrBuilder->addPropertyValue('decisionVoters', $defaultVoters);
            $parserContext->getRegistry()->registerBeanDefinition(Bee_Security_Config_IBeanIds::ACCESS_MANAGER, $accessMgrBuilder->getBeanDefinition());
        }
    }
}