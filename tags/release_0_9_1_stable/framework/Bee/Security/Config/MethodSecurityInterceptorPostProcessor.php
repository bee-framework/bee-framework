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
 * Time: 10:29:38 PM
 */

class Bee_Security_Config_MethodSecurityInterceptorPostProcessor implements Bee_Context_Config_IBeanPostProcessor, Bee_Context_Config_IContextAware {

    /**
     * @var Bee_IContext      
     */
    private $beeContext;

    public function postProcessBeforeInitialization($bean, $beanName) {

        if(Bee_Security_Config_IBeanIds::METHOD_SECURITY_INTERCEPTOR == $beanName &&
                $this->beeContext->containsBean(Bee_Security_Config_IBeanIds::AFTER_INVOCATION_MANAGER)) {
            Bee_Utils_Logger::debug("Setting AfterInvocationManaer on MethodSecurityInterceptor");
            $bean->setAfterInvocationManager($this->beeContext->getBean(Bee_Security_Config_IBeanIds::AFTER_INVOCATION_MANAGER));
        }

        return $bean;
    }

    public function postProcessAfterInitialization($bean, $beanName) {
        return $bean;
    }

    public function setBeeContext(Bee_IContext $context) {
        $this->beeContext = $context;
    }

}
