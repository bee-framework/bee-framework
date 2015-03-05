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
use Bee\Security\ConfigAttribute;
use Bee\Security\ConfigAttributeDefinition;
use Bee\Security\IAuthentication;

/**
 * Interface IProvider
 * @package Bee\Security\AfterInvocation
 */
interface IProvider {

    public function decide(IAuthentication $authentication, $object, ConfigAttributeDefinition $config,
        $returnedObject);

    /**
     * Indicates whether this <code>AfterInvocationProvider</code> is able to participate in a decision
     * involving the passed <code>ConfigAttribute</code>.<p>This allows the
     * <code>AbstractSecurityInterceptor</code> to check every configuration attribute can be consumed by the
     * configured <code>AccessDecisionManager</code> and/or <code>RunAsManager</code> and/or
     * <code>AccessDecisionManager</code>.</p>
     *
     * @param ConfigAttribute $attribute a configuration attribute that has been configured against the
     *        <code>AbstractSecurityInterceptor</code>
     *
     * @return true if this <code>AfterInvocationProvider</code> can support the passed configuration attribute
     */
    public function supports(ConfigAttribute $attribute);

    /**
     * Indicates whether the <code>AfterInvocationProvider</code> is able to provide "after invocation"
     * processing for the indicated secured object type.
     *
     * @param string $classOrClassName the class of secure object that is being queried
     *
     * @return true if the implementation can process the indicated class
     */
    public function supportsClass($classOrClassName);
}
