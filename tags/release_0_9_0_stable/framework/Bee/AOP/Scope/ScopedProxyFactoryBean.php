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
 * Time: 7:18:00 PM
 */

class Bee_AOP_Scope_ScopedProxyFactoryBean implements Bee_Context_IFactoryBean, Bee_Context_Config_IContextAware {

    /**
     * The name of the target bean
     * @var string
     */
    private $targetBeanName;

    private $proxy;

    /**
     * @var Bee_IContext 
     */
    private $beeContext;

    /**
     * @param string $targetBeanName
     * @return void
     */
    public function setTargetBeanName($targetBeanName) {
        $this->targetBeanName = $targetBeanName;
    }

    function setBeeContext(Bee_IContext $context) {
        $this->beeContext = $context;
    }

    function getObject() {
        return new Test_ClassToProxy_Proxy($this->beeContext->getIdentifier(), $this->targetBeanName);
    }

    function getObjectType() {
        if($this->proxy != null) {
            get_class($this->proxy);
        }
    }

    function isSingleton() {
        return true; 
    }
}
