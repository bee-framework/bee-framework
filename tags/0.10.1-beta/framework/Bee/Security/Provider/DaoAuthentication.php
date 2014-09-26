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

class Bee_Security_Provider_DaoAuthentication extends Bee_Security_Provider_AbstractUserDetailsAuthentication {
	
	/**
	 * Enter description here...
	 *
	 * @var Bee_Security_IPasswordEncoder
	 */
    private $passwordEncoder;

    /**
     * Enter description here...
     *
     * @var Bee_Security_Provider_ISaltSource
     */
    private $saltSource = null;

    /**
     * Enter description here...
     *
     * @var Bee_Security_IUserDetailsService
     */
    private $userDetailsService;

    public function __construct() {
		$this->passwordEncoder = new Bee_Security_PasswordEncoder_PlainText();
    }
    
    protected function additionalAuthenticationChecks(Bee_Security_IUserDetails $userDetails,
            Bee_Security_UsernamePasswordAuthenticationToken $authentication) {
		$salt = null;

        if (!is_null($this->saltSource)) {
            $salt = $this->saltSource->getSalt($userDetails);
        }

        $credentials = $authentication->getCredentials();
        if (is_null($credentials)) {
            throw new Bee_Security_Exception_BadCredentials('Bad credentials', $userDetails);
        }

        $presentedPassword = is_object($credentials) ? $credentials->__toString() : $credentials;

        if (!$this->passwordEncoder->isPasswordValid($userDetails->getPassword(), $presentedPassword, $salt)) {
            throw new Bee_Security_Exception_BadCredentials('Bad credentials', $userDetails);
        }
        
        
    }

    protected final function retrieveUser($username, Bee_Security_UsernamePasswordAuthenticationToken $authentication) {
        $loadedUser = null;

        try {
            $loadedUser = $this->getUserDetailsService()->loadUserByUsername($username);
        } catch (Bee_Persistence_Exception_DataAccess $repositoryProblem) {
            throw new Bee_Security_Exception_Authentication($repositoryProblem->getMessage(), null, $repositoryProblem);
        }

        if (is_null($loadedUser)) {
            throw new Bee_Security_Exception_Authentication(
                    "UserDetailsService returned null, which is an interface contract violation");
        }
        return $loadedUser;
    }
    
    /**
     * Enter description here...
     *
     * @return Bee_Security_IPasswordEncoder
     */
    public final function getPasswordEncoder() {
    	return $this->passwordEncoder;
    }

    /**
     * Enter description here...
     *
     * @param Bee_Security_IPasswordEncoder $passworEncoder
     * @return void
     */
    public final function setPasswordEncoder(Bee_Security_IPasswordEncoder $passworEncoder) {
    	$this->passwordEncoder = $passworEncoder; 
    }
    
    /**
     * Enter description here...
     *
     * @return Bee_Security_Provider_ISaltSource
     */
    public final function getSaltSource() {
    	return $this->saltSource;
    }
    
    /**
     * Enter description here...
     *
     * @param Bee_Security_Provider_ISaltSource $saltSource
     * @return void
     */
    public final function setSaltSource(Bee_Security_Provider_ISaltSource $saltSource) {
    	 $this->saltSource = $saltSource;
    }
    
    /**
     * Enter description here...
     *
     * @return Bee_Security_IUserDetailsService
     */
    protected function getUserDetailsService() {
        return $this->userDetailsService;
    }

    /**
     * Enter description here...
     *
     * @param Bee_Security_IUserDetailsService $userDetailsService
     * @return void
     */
    public final function setUserDetailsService(Bee_Security_IUserDetailsService $userDetailsService) {
        $this->userDetailsService = $userDetailsService;
    }
}
