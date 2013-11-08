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
 * Represents an authentication request.
 *
 * <p>
 * An <code>Authentication</code> object is not considered authenticated until
 * it is processed by an {@link Bee_Security_AuthenticationManager}.
 * </p>
 *
 * <p>
 * Stored in a request {@link Bee_Security_Context}.
 * </p>
 *
 */
interface Bee_Security_IAuthentication {
	
	/**
	 * Enter description here...
	 *
	 * @return string[]
	 */
	function getAuthorities();
	
	/**
	 * Enter description here...
	 *
	 * @return mixed
	 */
	function getCredentials();
	
	/**
	 * Enter description here...
	 *
	 * @return mixed
	 */
	function getDetails();
	
	/**
	 * Enter description here...
	 *
	 * @return mixed
	 */
	function getPrincipal();
	
	/**
	 * Enter description here...
	 *
	 * @return boolean
	 */
	function isAuthenticated();
	
	/**
	 * Enter description here...
	 *
	 * @param boolean $authenticated
	 * 
	 * @return void
	 */
	function setAuthenticated($authenticated);
}
?>