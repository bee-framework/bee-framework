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
use ReflectionClass;

/**
 * Interface IRunAsManager
 * @package Bee\Security
 */
interface IRunAsManager {
    //~ Methods ========================================================================================================

    /**
     * Returns a replacement <code>Authentication</code> object for the current secure object invocation, or
     * <code>null</code> if replacement not required.
     *
     * @param IAuthentication $authentication the caller invoking the secure object
     * @param mixed $object the secured object being called
     * @param ConfigAttributeDefinition $config the configuration attributes associated with the secure object being invoked
     *
     * @return IAuthentication a replacement object to be used for duration of the secure object invocation, or <code>null</code> if
     *         the <code>Authentication</code> should be left as is
     */
    function buildRunAs(IAuthentication $authentication, $object, ConfigAttributeDefinition $config);

    /**
     * Indicates whether this <code>RunAsManager</code> is able to process the passed
     * <code>ConfigAttribute</code>.<p>This allows the <code>AbstractSecurityInterceptor</code> to check every
     * configuration attribute can be consumed by the configured <code>AccessDecisionManager</code> and/or
     * <code>RunAsManager</code> and/or <code>AfterInvocationManager</code>.</p>
     *
     * @param ConfigAttribute $attribute a configuration attribute that has been configured against the
     *        <code>AbstractSecurityInterceptor</code>
     *
     * @return boolean <code>true</code> if this <code>RunAsManager</code> can support the passed configuration attribute
     */
    function supports(ConfigAttribute $attribute);

    /**
     * Indicates whether the <code>RunAsManager</code> implementation is able to provide run-as replacement for
     * the indicated secure object type.
     *
     * @param ReflectionClass|string $classOrClassName the class that is being queried
     *
     * @return boolean true if the implementation can process the indicated class
     */
    function supportsClass($classOrClassName);

}