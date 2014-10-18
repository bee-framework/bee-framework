<?php
namespace Bee\Security\Provider;
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

use Bee\Persistence\Exception\DataAccessException;
use Bee\Security\Exception\AuthenticationException;
use Bee\Security\Exception\BadCredentialsException;
use Bee\Security\IPasswordEncoder;
use Bee\Security\IUserDetails;
use Bee\Security\IUserDetailsService;
use Bee\Security\UsernamePasswordAuthenticationToken;
use Bee_Security_PasswordEncoder_PlainText;

/**
 * Class DaoAuthentication
 * @package Bee\Security\Provider
 */
class DaoAuthentication extends AbstractUserDetailsAuthentication {
	
	/**
	 * Enter description here...
	 *
	 * @var IPasswordEncoder
	 */
    private $passwordEncoder;

    /**
     * Enter description here...
     *
     * @var ISaltSource
     */
    private $saltSource = null;

    /**
     * Enter description here...
     *
     * @var IUserDetailsService
     */
    private $userDetailsService;

    public function __construct() {
		$this->passwordEncoder = new Bee_Security_PasswordEncoder_PlainText();
    }
    
    protected function additionalAuthenticationChecks(IUserDetails $userDetails,  UsernamePasswordAuthenticationToken $authentication) {
		$salt = null;

        if (!is_null($this->saltSource)) {
            $salt = $this->saltSource->getSalt($userDetails);
        }

        $credentials = $authentication->getCredentials();
        if (is_null($credentials)) {
            throw new BadCredentialsException('Bad credentials', $userDetails);
        }

        $presentedPassword = is_object($credentials) ? $credentials->__toString() : $credentials;

        if (!$this->passwordEncoder->isPasswordValid($userDetails->getPassword(), $presentedPassword, $salt)) {
            throw new BadCredentialsException('Bad credentials', $userDetails);
        }
        
        
    }

    protected final function retrieveUser($username, UsernamePasswordAuthenticationToken $authentication) {
        $loadedUser = null;

        try {
            $loadedUser = $this->getUserDetailsService()->loadUserByUsername($username);
        } catch (DataAccessException $repositoryProblem) {
            throw new AuthenticationException($repositoryProblem->getMessage(), null, $repositoryProblem);
        }

        if (is_null($loadedUser)) {
            throw new AuthenticationException("UserDetailsService returned null, which is an interface contract violation");
        }
        return $loadedUser;
    }
    
    /**
     * Enter description here...
     *
     * @return IPasswordEncoder
     */
    public final function getPasswordEncoder() {
    	return $this->passwordEncoder;
    }

    /**
     * Enter description here...
     *
     * @param IPasswordEncoder $passworEncoder
     * @return void
     */
    public final function setPasswordEncoder(IPasswordEncoder $passworEncoder) {
    	$this->passwordEncoder = $passworEncoder; 
    }
    
    /**
     * Enter description here...
     *
     * @return ISaltSource
     */
    public final function getSaltSource() {
    	return $this->saltSource;
    }
    
    /**
     * Enter description here...
     *
     * @param ISaltSource $saltSource
     * @return void
     */
    public final function setSaltSource(ISaltSource $saltSource) {
    	 $this->saltSource = $saltSource;
    }
    
    /**
     * Enter description here...
     *
     * @return IUserDetailsService
     */
    protected function getUserDetailsService() {
        return $this->userDetailsService;
    }

    /**
     * Enter description here...
     *
     * @param IUserDetailsService $userDetailsService
     * @return void
     */
    public final function setUserDetailsService(IUserDetailsService $userDetailsService) {
        $this->userDetailsService = $userDetailsService;
    }
}
