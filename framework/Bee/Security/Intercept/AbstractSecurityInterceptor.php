<?php
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
use Bee\Security\ConfigAttributeDefinition;
use Bee\Security\Context\SecurityContextHolder;
use Bee\Security\Exception\AccessDeniedException;
use Bee\Security\Exception\AuthenticationCredentialsNotFoundException;
use Bee\Security\IAccessDecisionManager;
use Bee\Security\IAfterInvocationManager;
use Bee\Security\IAuthenticationManager;
use Bee\Security\IRunAsManager;
use Bee\Utils\Assert;
use Bee\Utils\Types;

/**
 * Created by IntelliJ IDEA.
 * User: mp
 * Date: Feb 19, 2010
 * Time: 9:32:43 PM
 * To change this template use File | Settings | File Templates.
 */

abstract class Bee_Security_Intercept_AbstractSecurityInterceptor implements IInitializingBean {
    use \Bee\Utils\TLogged;

    /**
     * @var IAccessDecisionManager
     */
    private $accessDecisionManager;

    /**
     * @var IAfterInvocationManager
     */
    private $afterInvocationManager;

    /**
     * @var IAuthenticationManager
     */
    private $authenticationManager;

    /**
     * @var IRunAsManager
     */
    private $runAsManager;

    /**
     * @var boolean
     */
    private $alwaysReauthenticate = false;

    /**
     * @var boolean
     */
    private $rejectPublicInvocations = false;

    /**
     * @var boolean
     */
    private $validateConfigAttributes = true;

    public function __construct() {
        $this->runAsManager = new Bee_Security_Runas_NullRunAsManager();
    }

	/**
	 * Completes the work of the <tt>AbstractSecurityInterceptor</tt> after the secure object invocation has been
	 * completed.
	 *
	 * @param Bee_Security_Intercept_InterceptorStatusToken $token as returned by the {@link #beforeInvocation(Object)}} method
	 * @param mixed $returnedObject any object returned from the secure object invocation (may be <tt>null</tt>)
	 * @throws AccessDeniedException
	 * @throws Exception
	 * @return mixed the object the secure object invocation should ultimately return to its caller (may be <tt>null</tt>)
	 */
    protected function afterInvocation(Bee_Security_Intercept_InterceptorStatusToken $token, $returnedObject) {
        if ($token == null) {
            // public object
            return $returnedObject;
        }

        if ($token->isContextHolderRefreshRequired()) {
            if ($this->getLog()->isDebugEnabled()) {
				$this->getLog()->debug("Reverting to original Authentication: " . $token->getAuthentication());
            }

            SecurityContextHolder::getContext()->setAuthentication($token->getAuthentication());
        }

        if ($this->afterInvocationManager != null) {
            // Attempt after invocation handling
            try {
                $returnedObject = $this->afterInvocationManager->decide($token->getAuthentication(), $token->getSecureObject(),
                        $token->getAttr(), $returnedObject);
            }
            catch (AccessDeniedException $accessDeniedException) {
                // todo: what's with the event publishing...?
//                AuthorizationFailureEvent event = new AuthorizationFailureEvent(token.getSecureObject(), token
//                        .getAttr(), token.getAuthentication(), accessDeniedException);
//                publishEvent(event);

                throw $accessDeniedException;
            }
        }

        return $returnedObject;
    }

    public function afterPropertiesSet() {
        Assert::notNull($this->getSecureObjectClassName(), "Subclass must provide a non-null response to getSecureObjectClass()");
//        Assert::notNull($this->messages, "A message source must be set");
        Assert::notNull($this->authenticationManager, "An AuthenticationManager is required");
        Assert::notNull($this->accessDecisionManager, "An AccessDecisionManager is required");
        Assert::notNull($this->runAsManager, "A RunAsManager is required");
        Assert::notNull($this->obtainObjectDefinitionSource(), "An ObjectDefinitionSource is required");
        Assert::isTrue($this->obtainObjectDefinitionSource()->supports($this->getSecureObjectClassName()),
                "ObjectDefinitionSource does not support secure object class: " . $this->getSecureObjectClassName());
		Assert::isTrue($this->runAsManager->supportsClass($this->getSecureObjectClassName()),
                "RunAsManager does not support secure object class: " . $this->getSecureObjectClassName());
		Assert::isTrue($this->accessDecisionManager->supportsClass($this->getSecureObjectClassName()),
                "AccessDecisionManager does not support secure object class: " . $this->getSecureObjectClassName());

        if ($this->afterInvocationManager != null) {
			Assert::isTrue($this->afterInvocationManager->supportsClass($this->getSecureObjectClassName()),
                    "AfterInvocationManager does not support secure object class: " . $this->getSecureObjectClassName());
        }

        if ($this->validateConfigAttributes) {
            $attributeDefs = $this->obtainObjectDefinitionSource()->getConfigAttributeDefinitions();

            if ($attributeDefs == null) {
				$this->getLog()->warn("Could not validate configuration attributes as the ObjectDefinitionSource did not return "
                        . "a ConfigAttributeDefinition collection");
                return;
            }

            $unsupportedAttrs = array();

            foreach($attributeDefs as $def) {
                $attributes = $def->getConfigAttributes();

                foreach ($attributes as $attr) {

                    if (!$this->runAsManager->supports($attr) && !$this->accessDecisionManager->supports($attr)
                            && (($this->afterInvocationManager == null) || !$this->afterInvocationManager->supports($attr))) {
                        $unsupportedAttrs[] = $attr;
                    }
                }
            }

            if (count($unsupportedAttrs) != 0) {
                throw new InvalidArgumentException("Unsupported configuration attributes: " . $unsupportedAttrs);
            }

			$this->getLog()->info("Validated configuration attributes");
        }
    }

	/**
	 * @access protected
	 *
	 * @param $object
	 * @throws AccessDeniedException
	 * @throws AuthenticationCredentialsNotFoundException
	 * @throws Exception
	 * @return Bee_Security_Intercept_InterceptorStatusToken
	 */
    protected function beforeInvocation($object) {
		Assert::notNull($object, "Object was null");

        if (!Types::isAssignable(get_class($object), $this->getSecureObjectClassName())) {
            throw new InvalidArgumentException("Security invocation attempted for object "
                    . get_class($object)
                    . " but AbstractSecurityInterceptor only configured to support secure objects of type: "
                    . $this->getSecureObjectClassName());
        }

        $attr = $this->obtainObjectDefinitionSource()->getAttributes($object);

        if ($attr == null) {
            if ($this->rejectPublicInvocations) {
                throw new InvalidArgumentException(
                        "No public invocations are allowed via this AbstractSecurityInterceptor. "
                                . "This indicates a configuration error because the "
                                . "AbstractSecurityInterceptor.rejectPublicInvocations property is set to 'true'");
            }

            if ($this->getLog()->isDebugEnabled()) {
                $this->getLog()->debug("Public object - authentication not attempted");
            }

//            publishEvent(new PublicInvocationEvent(object));

            return null; // no further work post-invocation
        }

        if ($this->getLog()->isDebugEnabled()) {
            $this->getLog()->debug("Secure object: $object; ConfigAttributes: $attr");
        }

        if (SecurityContextHolder::getContext()->getAuthentication() == null) {
            $this->credentialsNotFound("An Authentication object was not found in the SecurityContext", $object, $attr);
        }

        $authenticated = $this->authenticateIfRequired();

        // Attempt authorization
        try {
            $this->accessDecisionManager->decide($authenticated, $object, $attr);
        }
        catch (AccessDeniedException $accessDeniedException) {
//            AuthorizationFailureEvent event = new AuthorizationFailureEvent(object, attr, authenticated,
//                    accessDeniedException);
//            publishEvent(event);

            throw $accessDeniedException;
        }

        if ($this->getLog()->isDebugEnabled()) {
            $this->getLog()->debug("Authorization successful");
        }
//
//        $event = new AuthorizedEvent(object, attr, authenticated);
//        publishEvent(event);

        // Attempt to run as a different user
        $runAs = $this->runAsManager->buildRunAs($authenticated, $object, $attr);

        if ($runAs == null) {
            if ($this->getLog()->isDebugEnabled()) {
                $this->getLog()->debug("RunAsManager did not change Authentication object");
            }

            // no further work post-invocation
            return new Bee_Security_Intercept_InterceptorStatusToken($authenticated, false, $attr, $object);
        } else {
            if ($this->getLog()->isDebugEnabled()) {
                $this->getLog()->debug("Switching to RunAs Authentication: " . $runAs);
            }

			SecurityContextHolder::getContext()->setAuthentication($runAs);

            // revert to token.Authenticated post-invocation
            return new Bee_Security_Intercept_InterceptorStatusToken($authenticated, true, $attr, $object);
        }
    }

    /**
     * Checks the current authentication token and passes it to the AuthenticationManager if
     * {@link org.springframework.security.Authentication#isAuthenticated()} returns false or the property
     * <tt>alwaysReauthenticate</tt> has been set to true.
     *
     * @return an authenticated <tt>Authentication</tt> object.
     */
    private function authenticateIfRequired() {
        $authentication = SecurityContextHolder::getContext()->getAuthentication();

        if ($authentication->isAuthenticated() && !$this->alwaysReauthenticate) {
            if ($this->getLog()->isDebugEnabled()) {
                $this->getLog()->debug("Previously Authenticated: " . $authentication);
            }

            return $authentication;
        }

        $authentication = $this->authenticationManager->authenticate($authentication);

        // We don't authenticated.setAuthentication(true), because each provider should do that
        if ($this->getLog()->isDebugEnabled()) {
            $this->getLog()->debug("Successfully Authenticated: " . $authentication);
        }

        SecurityContextHolder::getContext()->setAuthentication($authentication);

        return $authentication;
    }

    private function credentialsNotFound($reason, $secureObject, ConfigAttributeDefinition $configAttribs) {
        $exception = new AuthenticationCredentialsNotFoundException($reason);

//        AuthenticationCredentialsNotFoundEvent event = new AuthenticationCredentialsNotFoundEvent(secureObject,
//                configAttribs, exception);
//        publishEvent(event);

        throw $exception;
    }

    /**
     * Gets the AccessDecisionManager
     *
     * @return IAccessDecisionManager $accessDecisionManager
     */
    public function getAccessDecisionManager() {
        return $this->accessDecisionManager;
    }

    /**
     * Sets the AccessDecisionManager
     *
     * @param $accessDecisionManager IAccessDecisionManager
     * @return void
     */
    public function setAccessDecisionManager(IAccessDecisionManager $accessDecisionManager) {
        $this->accessDecisionManager = $accessDecisionManager;
    }


    /**
     * Gets the AfterInvocationManager
     *
     * @return IAfterInvocationManager
     */
    public function getAfterInvocationManager() {
        return $this->afterInvocationManager;
    }

    /**
     * Sets the AfterInvocationManager
     *
     * @param IAfterInvocationManager $afterInvocationManager
     * @return void
     */
    public function setAfterInvocationManager(IAfterInvocationManager $afterInvocationManager) {
        $this->afterInvocationManager = $afterInvocationManager;
    }

    /**
     * Gets the AuthenticationManager
     *
     * @return IAuthenticationManager $authenticationManager
     */
    public function getAuthenticationManager() {
        return $this->authenticationManager;
    }

    /**
     * Sets the AuthenticationManager
     *
     * @param $authenticationManager IAuthenticationManager
     * @return void
     */
    public function setAuthenticationManager(IAuthenticationManager $authenticationManager) {
        $this->authenticationManager = $authenticationManager;
    }

    /**
     * Gets the RunAsManager
     *
     * @return IRunAsManager $runAsManager
     */
    public function getRunAsManager() {
        return $this->runAsManager;
    }

    /**
     * Sets the RunAsManager
     *
     * @param $runAsManager IRunAsManager
     * @return void
     */
    public function setRunAsManager(IRunAsManager $runAsManager) {
        $this->runAsManager = $runAsManager;
    }

    /**
     * Indicates the type of secure objects the subclass will be presenting to
     * the abstract parent for processing. This is used to ensure collaborators
     * wired to the <code>AbstractSecurityInterceptor</code> all support the
     * indicated secure object class.
     *
     * @return string the type of secure object the subclass provides services for
     */
    public abstract function getSecureObjectClassName();

    /**
     * Indicates whether the <code>AbstractSecurityInterceptor</code> should
     * ignore the {@link Authentication#isAuthenticated()} property. Defaults to
     * <code>false</code>, meaning by default the
     * <code>Authentication.isAuthenticated()</code> property is trusted and
     * re-authentication will not occur if the principal has already been
     * authenticated.
     *
     * @param boolean $alwaysReauthenticate <code>true</code> to force <code>AbstractSecurityInterceptor</code> to
     * disregard the value of <code>Authentication.isAuthenticated()</code> and always re-authenticate the request
     * (defaults to <code>false</code>).
     */
    public function setAlwaysReauthenticate($alwaysReauthenticate) {
        $this->alwaysReauthenticate = $alwaysReauthenticate;
    }

    /**
     * @return bool
     */
    public function isAlwaysReauthenticate() {
        return $this->alwaysReauthenticate;
    }

    /**
     * By rejecting public invocations (and setting this property to <tt>true</tt>), essentially you are ensuring
     * that every secure object invocation advised by <code>AbstractSecurityInterceptor</code> has a configuration
     * attribute defined. This is useful to ensure a "fail safe" mode where undeclared secure objects will be rejected
     * and configuration omissions detected early. An <tt>IllegalArgumentException</tt> will be thrown by the
     * <tt>AbstractSecurityInterceptor</tt> if you set this property to <tt>true</tt> and an attempt is made to invoke
     * a secure object that has no configuration attributes.
     *
     * @param boolean $rejectPublicInvocations set to <code>true</code> to reject invocations of secure objects that have no
     * configuration attributes (by default it is <code>false</code> which treats undeclared secure objects
     * as "public" or unauthorized).
     */
    public function setRejectPublicInvocations($rejectPublicInvocations) {
        $this->rejectPublicInvocations = $rejectPublicInvocations;
    }

    /**
     * @return bool
     */
    public function isRejectPublicInvocations() {
        return $this->rejectPublicInvocations;
    }

    public function setValidateConfigAttributes($validateConfigAttributes) {
        $this->validateConfigAttributes = $validateConfigAttributes;
    }

    /**
     * @return bool
     */
    public function isValidateConfigAttributes() {
        return $this->validateConfigAttributes;
    }

    /**
     * @abstract
     * @return Bee_Security_Intercept_IObjectDefinitionSource
     */
    public abstract function obtainObjectDefinitionSource();
}