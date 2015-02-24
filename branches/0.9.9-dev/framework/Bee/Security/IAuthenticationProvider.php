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
use Bee\Security\Exception\AuthenticationException;

/**
 * Indicates a class can process a specific  {@link IAuthentication} implementation.
 *
 */
interface IAuthenticationProvider {
	
	/**
	 * Performs authentication with the same contract as {@link IAuthenticationManager#authenticate(IAuthentication)}.
	 *
	 * @param IAuthentication $authentication the authentication request object.
	 * 
	 * @return IAuthentication a fully authenticated object including credentials. May return <code>null</code> if the
     *         <code>AuthenticationProvider</code> is unable to support authentication of the passed
     *         <code>Authentication</code> object. In such a case, the next <code>AuthenticationProvider</code> that
     *         supports the presented <code>Authentication</code> class will be tried.
     * 
     * @throws AuthenticationException if authentication fails.
	 */
    function authenticate(IAuthentication $authentication);
	
    /**
     * Enter description here...
     *
     * @param String $authenticationClass
     * @return boolean
     */
    function supports($authenticationClass);
}
