<?php
namespace Bee\Security\Vote;
/*
 * Copyright 2008-2015 the original author or authors.
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
use Bee\Security\ConfigAttributeDefinition;
use Bee\Security\IAuthentication;
use Bee\Security\IConfigAttribute;

/**
 * Indicates a class is responsible for voting on authorization decisions.
 *
 * <p>
 * The coordination of voting (ie polling <code>AccessDecisionVoter</code>s,
 * tallying their responses, and making the final authorization decision) is
 * performed by an {@link org.springframework.security.AccessDecisionManager}.
 * </p>
 * 
 */
interface IAccessDecisionVoter {
	
	const ACCESS_GRANTED = 1;
	const ACCESS_ABSTAIN = 0;
	const ACCESS_DENIED = -1;

	/**
	 * Indicates whether this <code>AccessDecisionVoter</code> is able to vote on the passed
     * <code>ConfigAttribute</code>.<p>This allows the <code>AbstractSecurityInterceptor</code> to check every
     * configuration attribute can be consumed by the configured <code>AccessDecisionManager</code> and/or
     * <code>RunAsManager</code> and/or <code>AfterInvocationManager</code>.</p>
     * 
	 * @param IConfigAttribute $configAttribute a configuration attribute that has been configured against the
     *        <code>AbstractSecurityInterceptor</code>
	 * @return boolean true if this <code>AccessDecisionVoter</code> can support the passed configuration attribute
	 */
	public function supports(IConfigAttribute $configAttribute);

	/**
	 * Indicates whether the <code>AccessDecisionVoter</code> implementation is able to provide access control
     * votes for the indicated secured object type.
     * 
	 * @param $className
	 * @return boolean true if the implementation can process the indicated class
	 */
	public function supportsClass($className);

	/**
	 * Indicates whether or not access is granted.
     * <p>The decision must be affirmative (<code>ACCESS_GRANTED</code>), negative (<code>ACCESS_DENIED</code>)
     * or the <code>AccessDecisionVoter</code> can abstain (<code>ACCESS_ABSTAIN</code>) from voting.
     * Under no circumstances should implementing classes return any other value. If a weighting of results is desired,
     * this should be handled in a custom {@link org.springframework.security.AccessDecisionManager} instead.
     * </p>
     * <p>Unless an <code>AccessDecisionVoter</code> is specifically intended to vote on an access control
     * decision due to a passed method invocation or configuration attribute parameter, it must return
     * <code>ACCESS_ABSTAIN</code>. This prevents the coordinating <code>AccessDecisionManager</code> from counting
     * votes from those <code>AccessDecisionVoter</code>s without a legitimate interest in the access control
     * decision.
     * </p>
     * <p>Whilst the method invocation is passed as a parameter to maximise flexibility in making access
     * control decisions, implementing classes must never modify the behaviour of the method invocation (such as
     * calling <Code>MethodInvocation.proceed()</code>).</p>
     * 
	 * @param IAuthentication $authentication the caller invoking the method
	 * @param mixed $object the secured object
	 * @param ConfigAttributeDefinition $config the configuration attributes associated with the method being invoked
	 * @return int either {@link #ACCESS_GRANTED}, {@link #ACCESS_ABSTAIN} or {@link #ACCESS_DENIED}
	 */
	public function vote(IAuthentication $authentication, $object, ConfigAttributeDefinition $config);
}