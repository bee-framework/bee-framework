<?php
namespace Bee\Security\AfterInvocation;
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
use Bee\Context\Config\IInitializingBean;
use Bee\Framework;
use Bee\Security\ConfigAttribute;
use Bee\Security\ConfigAttributeDefinition;
use Bee\Security\IAfterInvocationManager;
use Bee\Security\IAuthentication;
use InvalidArgumentException;
use Logger;
use ReflectionClass;

/**
 * User: mp
 * Date: Feb 19, 2010
 * Time: 11:32:55 PM
 */

class ProviderManager implements IAfterInvocationManager, IInitializingBean {

	/**
	 * @var Logger
	 */
	protected static $log;

	/**
	 * @return Logger
	 */
	protected static function getLog() {
		if (!self::$log) {
			self::$log = Framework::getLoggerForClass(__CLASS__);
		}
		return self::$log;
	}

    //~ Instance fields ================================================================================================

    /**
     * @var IProvider[]
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

    public function decide(IAuthentication $authentication, $object, ConfigAttributeDefinition $config,
        $returnedObject) {

        $result = $returnedObject;
        foreach ($this->providers as $provider) {
            $result = $provider->decide($authentication, $object, $config, $result);
        }

        return $result;
    }

    /**
     * @return IProvider[]
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

    public function supports(ConfigAttribute $attribute) {

        foreach ($this->providers as $provider) {

            if (self::getLog()->isDebugEnabled()) {
				self::getLog()->debug("Evaluating $attribute against $provider");
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
	 * @param ReflectionClass|string $classOrClassName
	 *
	 * @return bool if the <code>AfterInvocationProviderManager</code> can support the secure object class, which requires
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
