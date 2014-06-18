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
 * Interface defining the minimum security information associated with the
 * current thread of execution.
 *
 * <p>
 * The security context is stored in a {@link Bee_Security_IContextHolder}.
 * </p>
 *
 */
class Bee_Security_Context {
	
	/**
	 * Enter description here...
	 *
	 * @var Bee_Security_IAuthentication
	 */
	private $authentication;
	
	/**
	 * Enter description here...
	 *
	 * @return Bee_Security_IAuthentication
	 */
    public function getAuthentication() {
    	return $this->authentication;
    }
    
    /**
     * Enter description here...
     *
     * @param Bee_Security_IAuthentication $authentication
     * @return void
     */
    public function setAuthentication(Bee_Security_IAuthentication $authentication=null) {
    	$this->authentication = $authentication;
    }
	
}
?>