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
use Bee\Security\AbstractAuthenticationManager;
use Bee\Security\AbstractAuthenticationToken;
use Bee\Security\Concurrent\ISessionController;
use Bee\Security\Concurrent\NullSessionController;
use Bee\Security\Exception\AccountStatusException;
use Bee\Security\Exception\AuthenticationException;
use Bee\Security\Exception\ConcurrentLoginException;
use Bee\Security\Exception\ProviderNotFoundException;
use Bee\Security\IAuthentication;
use Bee\Security\IAuthenticationProvider;

/**
 * Class Manager
 * @package Bee\Security\Provider
 */
class Manager extends AbstractAuthenticationManager {

	/**
	 * Enter description here...
	 *
	 * @var ISessionController
	 */
    private $sessionController;

    /**
     * Enter description here...
     *
     * @var IAuthenticationProvider[]
     */
    private $providers = array();

    //    protected MessageSourceAccessor messages = SpringSecurityMessageSource.getAccessor();
    // private $exceptionMappings = new Properties();
    //private $additionalExceptionMappings = new Properties();
    
    public function __construct() {
    	$this->sessionController = new NullSessionController();
    }

    protected function doAuthentication(IAuthentication $authentication) {
    	
        $lastException = null;
        
        foreach($this->providers as $provider) {
        	 
            if (!$provider->supports($authentication)) {
                continue;
            }

            try {
                $result = $provider->authenticate($authentication);

                if (!is_null($result)) {
                	$this->copyDetails($authentication, $result);
                	$this->sessionController->checkAuthenticationAllowed($result);
                }

            } catch (AuthenticationException $ae) {
                $lastException = $ae;
                $result = null;
            }

            // SEC-546: Avoid polling additional providers if auth failure is due to invalid account status or
            // disallowed concurrent login.
            if ($lastException instanceof AccountStatusException || $lastException instanceof ConcurrentLoginException) {
                break;
            }

            if ($result != null) {
                $this->sessionController->registerSuccessfulAuthentication($result);
//                publishEvent(new AuthenticationSuccessEvent(result));
                return $result;
            }
        }

        if ($lastException == null) {
            $lastException = new ProviderNotFoundException('No AuthenticationProvider found for class ' . get_class($authentication));
        }

//        publishAuthenticationFailure($lastException, $authentication);

        throw $lastException;    	
    }
    
    private function copyDetails(IAuthentication $source, IAuthentication $dest) {
        if (($dest instanceof AbstractAuthenticationToken) && ($dest->getDetails() == null)) {
            $dest->setDetails($source->getDetails());
        }
    }
    
    /**
     * Enter description here...
     *
     * @return IAuthenticationProvider[]
     */
    public final function getProviders() {
    	return $this->providers; 
    }
    
    /**
     * Enter description here...
     *
     * @param array $providers
     */
    public final function setProviders(array $providers) {
    	$this->providers = $providers;
    }

    /**
     * Enter description here...
     *
     * @return ISessionController
     */
    public final function getSessionController() {
    	return $this->sessionController;
    }
    
    /**
     * Enter description here...
     *
     * @param ISessionController $sessionController
     * 
     * @return void
     */
    public final function setSessionController(ISessionController $sessionController) {
    	$this->sessionController = $sessionController;
    }
}