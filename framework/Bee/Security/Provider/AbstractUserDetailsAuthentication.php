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

abstract class Bee_Security_Provider_AbstractUserDetailsAuthentication implements Bee_Security_IAuthenticationProvider {
	
	/**
	 * Enter description here...
	 *
	 * @var boolean
	 */
    protected $hideUserNotFoundExceptions = false;
    
    /**
     * Enter description here...
     *
     * @var boolean
     */
	private $forcePrincipalAsString = false;
	
	/**
	 * Allows subclasses to perform any additional checks of a returned (or cached) <code>Bee_Security_IUserDetails</code>
     * for a given authentication request. Generally a subclass will at least compare the {@link Bee_Security_IAuthentication#getCredentials()}
     * with a {@link Bee_Security_IUserDetails#getPassword()}. If custom logic is needed to compare additional properties of
     * <code>Bee_Security_IUserDetails</code> and/or <code>Bee_Security_UsernamePasswordAuthenticationToken</code>,
     * these should also appear in this method.
	 *
	 * @param Bee_Security_IUserDetails $userDetails
	 * @param Bee_Security_UsernamePasswordAuthenticationToken $authentication
	 * 
	 * @throws Bee_Security_Exception_Authentication
	 */
    protected abstract function additionalAuthenticationChecks(Bee_Security_IUserDetails $userDetails,
        Bee_Security_UsernamePasswordAuthenticationToken $authentication);


	private function defaultPreAuthenticationChecks(Bee_Security_IUserDetails $userDetails) {
		if (!$userDetails->isAccountNonLocked()) {
			throw new Bee_Security_Exception_AccountStatus('User account is locked', $userDetails);
		}

		if (!$userDetails->isEnabled()) {
			throw new Bee_Security_Exception_AccountStatus('User is disabled', $userDetails);
		}

		if (!$userDetails->isAccountNonExpired()) {
			throw new Bee_Security_Exception_AccountStatus('User account has expired', $userDetails);
		}
	}
	
	private function defaultPostAuthenticationChecks(Bee_Security_IUserDetails $userDetails) {
		if (!$userDetails->isCredentialsNonExpired()) {
			throw new Bee_Security_Exception_AccountStatus('User credentials have expired', $userDetails);
		}
	}

	/**
	 * Enter description here...
	 *
	 * @param Bee_Security_IAuthentication $authentication
	 * @throws Bee_Security_Exception_AccountStatus
	 * @throws Bee_Security_Exception_Authentication
	 * @throws Bee_Security_Exception_BadCredentials
	 * @throws Bee_Security_Exception_UsernameNotFound
	 * @throws Exception
	 * @return Bee_Security_IAuthentication
	 */
	public final function authenticate(Bee_Security_IAuthentication $authentication) {
		
		Bee_Utils_Assert::isInstanceOf('Bee_Security_UsernamePasswordAuthenticationToken', $authentication, 'Only UsernamePasswordAuthenticationToken is supported');

        // Determine username
        $username = is_null($authentication->getPrincipal()) ? "NONE_PROVIDED" : $authentication->getName();

        $cacheWasUsed = true;
        $user = null;
//        $user = $this->userCache->getUserFromCache($username);

        if (is_null($user)) {
            $cacheWasUsed = false;

            try {
                $user = $this->retrieveUser($username, $authentication);
            } catch (Bee_Security_Exception_UsernameNotFound $notFound) {
                if ($this->hideUserNotFoundExceptions) {
                    throw new Bee_Security_Exception_BadCredentials("Bad credentials");
                } else {
                    throw $notFound;
                }
            }

            Bee_Utils_Assert::notNull($user, 'retrieveUser returned null - a violation of the interface contract');
        }

//        $this->preAuthenticationChecks->check($user);
		$this->defaultPreAuthenticationChecks($user);
        
        try {
            $this->additionalAuthenticationChecks($user, $authentication);
        } catch (Bee_Security_Exception_Authentication $exception) {
            if ($cacheWasUsed) {
                // There was a problem, so try again after checking
                // we're using latest data (ie not from the cache)
//                $cacheWasUsed = false;
                $user = $this->retrieveUser($username, $authentication);
                $this->additionalAuthenticationChecks($user, $authentication);
            } else {
                throw $exception;
            }
        }

		$this->defaultPostAuthenticationChecks($user);
        
//        if (!$cacheWasUsed) {
//            $this->userCache->putUserInCache(user);
//        }

        $principalToReturn = $user;

        if ($this->forcePrincipalAsString) {
			$principalToReturn = $user->getUsername();
        }

        return $this->createSuccessAuthentication($principalToReturn, $authentication, $user);
        	
	}
	
    /**
     * Creates a successful {@link Bee_Security_IAuthentication} object.<p>Protected so subclasses can override.</p>
     *  <p>Subclasses will usually store the original credentials the user supplied (not salted or encoded
     * passwords) in the returned <code>Bee_Security_IAuthentication</code> object.</p>
     *
     * @param mixed $principal that should be the principal in the returned object (defined by the {@link
     *        #isForcePrincipalAsString()} method)
     * @param Bee_Security_IAuthentication $authentication that was presented to the provider for validation
     * @param Bee_Security_IUserDetails $user that was loaded by the implementation
     *
     * @return Bee_Security_UsernamePasswordAuthenticationToken the successful authentication token
     */
    protected function createSuccessAuthentication($principal, Bee_Security_IAuthentication $authentication, Bee_Security_IUserDetails $user) {
        // Ensure we return the original credentials the user supplied,
        // so subsequent attempts are successful even with encoded passwords.
        // Also ensure we return the original getDetails(), so that future
        // authentication events after cache expiry contain the details
        $result = new Bee_Security_UsernamePasswordAuthenticationToken($principal, $authentication->getCredentials(), $user->getAuthorities());
        $result->setDetails($authentication->getDetails());

        return $result;
    }

    /**
	 * Allows subclasses to actually retrieve the <code>Bee_Security_IUserDetails</code> from an implementation-specific
     * location, with the option of throwing an <code>Bee_Security_Exception_Authentication</code> immediately if the presented
     * credentials are incorrect (this is especially useful if it is necessary to bind to a resource as the user in
     * order to obtain or generate a <code>Bee_Security_IUserDetails</code>).<p>Subclasses are not required to perform any
     * caching, as the <code>Bee_Security_Provider_AbstractUserDetailsAuthentication</code> will by default cache the
     * <code>Bee_Security_IUserDetails</code>. The caching of <code>Bee_Security_IUserDetails</code> does present additional
     * complexity as this means subsequent requests that rely on the cache will need to still have their credentials validated,
     * even if the correctness of credentials was assured by subclasses adopting a binding-based strategy in this method.
     * Accordingly it is important that subclasses either disable caching (if they want to ensure that this method is
     * the only method that is capable of authenticating a request, as no <code>Bee_Security_IUserDetails</code> will ever be
     * cached) or ensure subclasses implement {@link #additionalAuthenticationChecks(Bee_Security_IUserDetails,
     * Bee_Security_UsernamePasswordAuthenticationToken)} to compare the credentials of a cached <code>Bee_Security_IUserDetails</code>
     * with subsequent authentication requests.</p>
     *  <p>Most of the time subclasses will not perform credentials inspection in this method, instead
     * performing it in {@link #additionalAuthenticationChecks(Bee_Security_IUserDetails, Bee_Security_UsernamePasswordAuthenticationToken)} so
     * that code related to credentials validation need not be duplicated across two methods.</p>
	 *
	 * @param String $username The username to retrieve
	 * @param Bee_Security_UsernamePasswordAuthenticationToken $authentication The authentication request, which subclasses <em>may</em> need
	 * 			to perform a binding-based retrieval of the <code>Bee_Security_IUserDetails</code>
	 * 
	 * @return Bee_Security_IUserDetails the user information (never <code>null</code> - instead an exception should the thrown)
	 * 
	 * @throws Bee_Security_Exception_Authentication if the credentials could not be validated (generally a
     *         <code>Bee_Security_Exception_BadCredentials</code>, an <code>Bee_Security_Exception_AuthenticationService</code> or
     *         <code>Bee_Security_Exception_UsernameNotFound</code>)
	 */
	protected abstract function retrieveUser($username, Bee_Security_UsernamePasswordAuthenticationToken $authentication);
	
    public function supports($authenticationClass) {
    	return Bee_Utils_Types::isAssignable($authenticationClass, 'Bee_Security_UsernamePasswordAuthenticationToken');
    }
	
	/**
	 * Enter description here...
	 *
	 * @return boolean
	 */
	public function isHideUserNotFoundExceptions() {
		return $this->hideUserNotFoundExceptions;
	}
	
	/**
	 * By default the <code>Bee_Security_Provider_AbstractUserDetailsAuthentication</code> throws a
     * <code>Bee_Security_Exception_BadCredentials</code> if a username is not found or the password is incorrect. Setting this
     * property to <code>false</code> will cause <code>Bee_Security_Exception_UsernameNotFound</code>s to be thrown instead for the
     * former. Note this is considered less secure than throwing <code>Bee_Security_Exception_BadCredentials</code> for both
     * exceptions.
	 *
	 * @param boolean $hideUserNotFoundExceptions
	 * 
	 * @return void
	 */
	public function setHideUserNotFoundExceptions($hideUserNotFoundExceptions) {
		$this->hideUserNotFoundExceptions = $hideUserNotFoundExceptions;
	}

	/**
	 * Enter description here...
	 *
	 * @return boolean
	 */
    public function isForcePrincipalAsString() {
        return $this->forcePrincipalAsString;
    }

    /**
     * Enter description here...
     *
     * @param boolean $forcePrincipalAsString
     */
    public function setForcePrincipalAsString($forcePrincipalAsString) {
        $this->forcePrincipalAsString = $forcePrincipalAsString;
    }
}
