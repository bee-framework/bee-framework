<?php
namespace Bee\Security;
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

/**
 * Interface IAccessDecisionManager
 * @package Bee\Security
 */
interface IAccessDecisionManager {

    /**
     * @abstract
     * @param IAuthentication $authentication the caller invoking the method
     * @param mixed $object the secured object being called
     * @param ConfigAttributeDefinition $configAttributes the configuration attributes associated with the
     * secured object being invoked
     * @return void
     */
    function decide(IAuthentication $authentication, $object, ConfigAttributeDefinition $configAttributes);

    /**
     * Indicates whether this <code>AccessDecisionManager</code> is able to process authorization requests
     * presented with the passed <code>ConfigAttribute</code>.<p>This allows the
     * <code>AbstractSecurityInterceptor</code> to check every configuration attribute can be consumed by the
     * configured <code>AccessDecisionManager</code> and/or <code>RunAsManager</code> and/or
     * <code>AfterInvocationManager</code>.</p>
     *
     * @abstract
     * @param IConfigAttribute $configAttribute a configuration attribute that has been configured against the
     *        <code>AbstractSecurityInterceptor</code>
     * @return boolean true if this <code>AccessDecisionManager</code> can support the passed configuration attribute
     */
    function supports(IConfigAttribute $configAttribute);

    /**
     * Indicates whether the <code>AccessDecisionManager</code> implementation is able to provide access
     * control decisions for the indicated secured object type.
     *
     * @abstract
     * @param string $className the class that is being queried
     * @return boolean <code>true</code> if the implementation can process the indicated class
     */
    function supportsClass($className);
}