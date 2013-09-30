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
 * @throws Exception
 *
 */
class Bee_Security_UsernamePasswordAuthenticationToken extends Bee_Security_AbstractAuthenticationToken {
	
    private $credentials;
    private $principal;
	
    /**
     * Enter description here...
     *
     * @param mixed $principal
     * @param mixed $credentials
     * @param Bee_Security_IGrantedAuthority[] $authorities
     */
    public function __construct($principal, $credentials, $authorities = null) {
    	parent::__construct($authorities);
    	$this->principal = $principal;
    	$this->credentials = $credentials;

    	parent::setAuthenticated(!is_null($authorities) ? true : false);
    }
    
	public function getPrincipal() {
		return $this->principal;
	}
	
	public function getCredentials() {
		return $this->credentials;
	}
	
	public function setAuthenticated($authenticated) {
        if ($authenticated) {
            throw new Exception("Cannot set this token to trusted - use constructor containing granted authority[]s instead");
        }
		parent::setAuthenticated(false);	
	}

    public function __toString() {
        return 'Bee_Security_UsernamePasswordAuthenticationToken['.$this->principal.']';
    }
}
?>