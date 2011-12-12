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
 * Time: 11:25:59 PM
 * To change this template use File | Settings | File Templates.
 */
interface Bee_Security_IAfterInvocationManager {

    /**
     * Given the details of a secure object invocation including its returned <code>Object</code>, make an
     * access control decision or optionally modify the returned <code>Object</code>.
     *
     * @param Bee_Security_IAuthentication $authentication the caller that invoked the method
     * @param mixed object the secured object that was called
     * @param Bee_Security_ConfigAttributeDefinition $config the configuration attributes associated with the secured object that was invoked
     * @param mixed returnedObject the <code>Object</code> that was returned from the secure object invocation
     *
     * @return mixed the <code>Object</code> that will ultimately be returned to the caller (if an implementation does not
     *         wish to modify the object to be returned to the caller, the implementation should simply return the
     *         same object it was passed by the <code>returnedObject</code> method argument)
     *
     * @throws Bee_Security_Exception_AccessDenied if access is denied
     */
    function decide(Bee_Security_IAuthentication $authentication, $object, Bee_Security_ConfigAttributeDefinition $config,
        $returnedObject);

    /**
     * Indicates whether this <code>AfterInvocationManager</code> is able to process "after invocation"
     * requests presented with the passed <code>ConfigAttribute</code>.<p>This allows the
     * <code>AbstractSecurityInterceptor</code> to check every configuration attribute can be consumed by the
     * configured <code>AccessDecisionManager</code> and/or <code>RunAsManager</code> and/or
     * <code>AfterInvocationManager</code>.</p>
     *
     * @param attribute a configuration attribute that has been configured against the
     *        <code>AbstractSecurityInterceptor</code>
     *
     * @return boolean true if this <code>AfterInvocationManager</code> can support the passed configuration attribute
     */
    function supports(Bee_Security_ConfigAttribute $attribute);

    /**
     * Indicates whether the <code>AfterInvocationManager</code> implementation is able to provide access
     * control decisions for the indicated secured object type.
     *
     * @param ReflectionClass|string $classOrClassName the class that is being queried
     *
     * @return boolean <code>true</code> if the implementation can process the indicated class
     */
    function supportsClass($classOrClassName);

}
?>