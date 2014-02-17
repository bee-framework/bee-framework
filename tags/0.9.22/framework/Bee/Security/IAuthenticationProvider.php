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
 * Indicates a class can process a specific  {@link Bee_Security_IAuthentication} implementation.
 *
 */
interface Bee_Security_IAuthenticationProvider {
	
	/**
	 * Performs authentication with the same contract as {@link Bee_Security_IAuthenticationManager#authenticate(Bee_Security_IAuthentication)}.
	 *
	 * @param Bee_Security_IAuthentication $authentication the authentication request object.
	 * 
	 * @return Bee_Security_IAuthentication a fully authenticated object including credentials. May return <code>null</code> if the
     *         <code>AuthenticationProvider</code> is unable to support authentication of the passed
     *         <code>Authentication</code> object. In such a case, the next <code>AuthenticationProvider</code> that
     *         supports the presented <code>Authentication</code> class will be tried.
     * 
     * @throws Bee_Security_AuthenticationException if authentication fails.
	 */
    function authenticate(Bee_Security_IAuthentication $authentication);
	
    /**
     * Enter description here...
     *
     * @param String $authenticationClass
     * @return boolean
     */
    function supports($authenticationClass);
}
?>