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


class Bee_Security_Provider_Manager extends Bee_Security_AbstractAuthenticationManager {

	/**
	 * Enter description here...
	 *
	 * @var Bee_Security_Concurrent_ISessionController
	 */
    private $sessionController;

    /**
     * Enter description here...
     *
     * @var array
     */
    private $providers = array();

    //    protected MessageSourceAccessor messages = SpringSecurityMessageSource.getAccessor();
    // private $exceptionMappings = new Properties();
    //private $additionalExceptionMappings = new Properties();
    
    public function __construct() {
    	$this->sessionController = new Bee_Security_Concurrent_NullSessionController();
    }

    protected function doAuthentication(Bee_Security_IAuthentication $authentication) {
    	
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

            } catch (Bee_Security_Exception_Authentication $ae) {
                $lastException = $ae;
                $result = null;
            }

            // SEC-546: Avoid polling additional providers if auth failure is due to invalid account status or
            // disallowed concurrent login.
            if ($lastException instanceof Bee_Security_Exception_AccountStatus || $lastException instanceof Bee_Security_Exception_ConcurrentLogin) {
                break;
            }

            if ($result != null) {
                $this->sessionController->registerSuccessfulAuthentication($result);
//                publishEvent(new AuthenticationSuccessEvent(result));
                return $result;
            }
        }

        if ($lastException == null) {
            $lastException = new Bee_Security_Exception_ProviderNotFound('No AuthenticationProvider found for class ' . get_class($authentication));
        }

//        publishAuthenticationFailure($lastException, $authentication);

        throw $lastException;    	
    }
    
    private function copyDetails(Bee_Security_IAuthentication $source, Bee_Security_IAuthentication $dest) {
        if (($dest instanceof Bee_Security_AbstractAuthenticationToken) && ($dest->getDetails() == null)) {
            $dest->setDetails($source->getDetails());
        }
    }
    
    /**
     * Enter description here...
     *
     * @return array<Bee_Security_IAuthenticationProvider>
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
     * @return Bee_Security_Concurrent_ISessionController
     */
    public final function getSessionController() {
    	return $this->sessionController;
    }
    
    /**
     * Enter description here...
     *
     * @param Bee_Security_Concurrent_ISessionController $sessionController
     * 
     * @return void
     */
    public final function setSessionController(Bee_Security_Concurrent_ISessionController $sessionController) {
    	$this->sessionController = $sessionController;
    }
}
?>