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
 * Time: 11:32:55 PM
 */

class Bee_Security_AfterInvocation_ProviderManager implements Bee_Security_IAfterInvocationManager, Bee_Context_Config_IInitializingBean {

    //~ Instance fields ================================================================================================

    /**
     * @var Bee_Security_AfterInvocation_IProvider[]
     */
    private $providers;

    //~ Methods ========================================================================================================

    public function afterPropertiesSet() {
        $this->checkIfValidList($this->providers);
    }

    private function checkIfValidList(array $listToCheck) {
        if ($listToCheck == null || count($listToCheck) == 0) {
            throw new InvalidArgumentException("A list of AfterInvocationProviders is required");
        }
    }

    public function decide(Bee_Security_IAuthentication $authentication, $object, Bee_Security_ConfigAttributeDefinition $config,
        $returnedObject) {

        $result = $returnedObject;
        foreach ($this->providers as $provider) {
            $result = $provider->decide($authentication, $object, $config, $result);
        }

        return $result;
    }

    /**
     * @return Bee_Security_AfterInvocation_IProvider[]
     */
    public function getProviders() {
        return $this->providers;
    }

    public function setProviders(array $newList) {
        $this->checkIfValidList($newList);

//        Iterator iter = newList.iterator();
//        while (iter.hasNext()) {
//            Object currentObject = iter.next();
//
//            Assert.isInstanceOf(AfterInvocationProvider.class, currentObject, "AfterInvocationProvider " +
//                    currentObject.getClass().getName() + " must implement AfterInvocationProvider");
//        }

        $this->providers = $newList;
    }

    public function supports(Bee_Security_ConfigAttribute $attribute) {

        foreach ($this->providers as $provider) {

            if (Bee_Utils_Logger::isDebugEnabled()) {
                Bee_Utils_Logger::debug("Evaluating $attribute against $provider");
            }

            if ($provider->supports($attribute)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Iterates through all <code>AfterInvocationProvider</code>s and ensures each can support the presented
     * class.<p>If one or more providers cannot support the presented class, <code>false</code> is returned.</p>
     *
     * @param clazz the secure object class being queries
     *
     * @return if the <code>AfterInvocationProviderManager</code> can support the secure object class, which requires
     *         every one of its <code>AfterInvocationProvider</code>s to support the secure object class
     */
    public function supportsClass($classOrClassName) {

        foreach ($this->providers as $provider) {
            if (!$provider->supports($classOrClassName)) {
                return false;
            }
        }

        return true;
    }

}
