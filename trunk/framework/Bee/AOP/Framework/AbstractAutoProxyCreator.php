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
use Bee\Context\Config\IInstantiationAwareBeanPostProcessor;
use Bee\Utils\IOrdered;

/**
 * User: mp
 * Date: Feb 20, 2010
 * Time: 12:25:41 AM
 */

class AbstractAutoProxyCreator implements IInstantiationAwareBeanPostProcessor, IOrdered {

    /**
     * @var int
     */
    private $order;

    /**
     * Gets the Order
     *
     * @return int $order
     */
    public function getOrder() {
        return $this->order;
    }

    /**
     * Sets the Order
     *
     * @param $order int
     * @return void
     */
    public function setOrder($order) {
        $this->order = $order;
    }

    function postProcessBeforeInstantiation($className, $beanName) {
        return null;
    }

    function postProcessAfterInstantiation($bean, $beanName) {
        return true;
    }

    public function postProcessBeforeInitialization($bean, $beanName) {
        return $bean;
    }

    public function postProcessAfterInitialization($bean, $beanName) {
        return $bean;
    }

    /**
     * Create an AOP proxy for the given bean.
     * @param string $beanClass the class of the bean
     * @param string $beanName the name of the bean
     * @param array $specificInterceptors the set of interceptors that is
     * specific to this bean (may be empty, but not null)
     * @param Bee_AOP_ITargetSource $targetSource the TargetSource for the proxy,
     * already pre-configured to access the bean
     * @return mixed the AOP proxy for the bean
     * @see #buildAdvisors
     */
//    protected function createProxy($beanClassName, $beanName, array $specificInterceptors, Bee_AOP_ITargetSource $targetSource) {
//
//        $proxyFactory = new ProxyFactory();
//        // Copy our properties (proxyTargetClass etc) inherited from ProxyConfig.
//        $proxyFactory->copyFrom($this);
//
//        $advisors = $buildAdvisors($beanName, $specificInterceptors);
//        for (int i = 0; i < advisors.length; i++) {
//            $proxyFactory->addAdvisor($advisors[i]);
//        }
//
//        proxyFactory.setTargetSource(targetSource);
//        customizeProxyFactory(proxyFactory);
//
//        proxyFactory.setFrozen(this.freezeProxy);
//        if (advisorsPreFiltered()) {
//            proxyFactory.setPreFiltered(true);
//        }
//
//        return proxyFactory.getProxy(this.proxyClassLoader);
//    }

}