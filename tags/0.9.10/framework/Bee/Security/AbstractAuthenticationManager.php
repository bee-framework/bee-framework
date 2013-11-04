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

abstract class Bee_Security_AbstractAuthenticationManager implements Bee_Security_IAuthenticationManager {

	/**
	 * Enter description here...
	 *
	 * @var booelan
	 */
	private $clearExtraInformation;

	public final function authenticate(Bee_Security_IAuthentication $authentication) {
        try {
            return $this->doAuthentication($authentication);
        } catch (Bee_Security_AuthenticationException $e) {
            $e->setAuthentication($authentication);

            if ($this->clearExtraInformation) {
                $e->clearExtraInformation();
            }

            throw $e;
        }
	}

	/**
	 * Enter description here...
	 *
	 * @param Bee_Security_IAuthentication $authentication
	 * 
	 * @throws Bee_Security_AuthenticationException
	 */
    protected abstract function doAuthentication(Bee_Security_IAuthentication $authentication);
	
    /**
     * If set to true, the <tt>extraInformation</tt> set on an <tt>Bee_Security_AuthenticationException</tt> will be cleared
     * before rethrowing it. This is useful for use with remoting protocols where the information shouldn't
     * be serialized to the client. Defaults to 'false'.
     * 
     * @TODO: do we actually need this?!?
     *
     * @param boolean $clearExtraInformation
     */
	public final function setClearExtraInformation($clearExtraInformation) {
		$this->clearExtraInformation = $clearExtraInformation;
	}
    
}
?>