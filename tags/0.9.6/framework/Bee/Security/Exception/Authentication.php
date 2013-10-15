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

class Bee_Security_Exception_Authentication extends Bee_Security_Exception_Generic {
	
	/**
	 * Enter description here...
	 *
	 * @var Bee_Security_IAuthentication
	 */
    private $authentication;
    
    /**
     * Enter description here...
     *
     * @var mixed
     */
    private $extraInformation;
    
    public function __construct($msg, $extraInformation  = null, Exception $cause = null) {
    	parent::__construct($msg, $cause);
    	$this->extraInformation = $extraInformation;
    }
    
    /**
     * Enter description here...
     *
     * @return Bee_Security_IAuthentication
     */
    public final function getAuthentication() {
    	return $this->authentication;
    }
    
    /**
     * Enter description here...
     *
     * @param Bee_Security_IAuthentication $authentication
     * @return void
     */
    public final function setAuthentication(Bee_Security_IAuthentication $authentication) {
    	$this->authentication = $authentication;
    }
    
    /**
     * Any additional information about the exception. Generally a <code>UserDetails</code> object.
     *
     * @return mixed
     */
    public final function getExtraInformation() {
    	return $this->extraInformation; 
    }
    
    /**
     * Enter description here...
     *
     * @return void
     */
    public final function clearExtraInformation() {
    	$this->extraInformation = null;
    }
}
?>