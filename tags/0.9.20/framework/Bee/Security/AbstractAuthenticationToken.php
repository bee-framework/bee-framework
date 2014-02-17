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

abstract class Bee_Security_AbstractAuthenticationToken implements Bee_Security_IAuthentication {
	
	/**
	 * Enter description here...
	 *
	 * @var mixed
	 */
    private $details;
    
    /**
     * Enter description here...
     *
     * @var Bee_Security_IGrantedAuthority[]
     */
    private $authorities;
    
    /**
     * Enter description here...
     *
     * @var boolean
     */
    private $authenticated = false;

	public function __construct(array $authorities = null) {
		if(!is_null($authorities)) {
			foreach($authorities as $authority) {
				Bee_Utils_Assert::notNull($authority, 'List of granted authorities passed to Bee_Security_AbstractAuthenticationToken must not contain null values!');
			}
		}
		$this->authorities = $authorities;
	}
	
	public function getAuthorities() {
		// @todo: make sure this does not return the original authorities array
		return $this->authorities;
	}
	
	public function getDetails() {
		return $this->details;
	}
	
	public function setDetails($details) {
		$this->details = $details;
	}
	
	public function isAuthenticated() {
		return $this->authenticated;
	}
	
	public function setAuthenticated($authenticated) {
		$this->authenticated = $authenticated;	
	}
	
	public function getName() {
		$principal = $this->getPrincipal();
		if(is_object($principal)) {
			if(Bee_Utils_Types::isAssignable($principal, 'Bee_Security_IUserDetails')) {
				return $principal->getUsername();
			} else {
				return $principal->__toString(); 
			}
		}
		return $principal;
	}
	
}
?>