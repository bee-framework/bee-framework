<?php
namespace Bee\Security;
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

use Bee\Utils\Assert;
use Bee\Utils\Types;

/**
 * Class AbstractAuthenticationToken
 * @package Bee\Security
 */
abstract class AbstractAuthenticationToken implements IAuthentication {
	
	/**
	 * Enter description here...
	 *
	 * @var mixed
	 */
    private $details;
    
    /**
     * Enter description here...
     *
     * @var IGrantedAuthority[]
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
				Assert::notNull($authority, 'List of granted authorities passed to Bee\Security\AbstractAuthenticationToken must not contain null values!');
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
			if(Types::isAssignable($principal, 'Bee\Security\IUserDetails')) {
				return $principal->getUsername();
			} else {
				return $principal->__toString(); 
			}
		}
		return $principal;
	}
}