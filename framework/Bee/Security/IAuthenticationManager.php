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
 * Interface IAuthenticationManager
 * Processes an {@link IAuthentication} request.
 * @package Bee\Security
 */
interface IAuthenticationManager {

	/**
     * Attempts to authenticate the passed {@link IAuthentication} object, returning a fully populated
     * <code>IAuthentication</code> object (including granted authorities) if successful.<p>An
     * <code>IAuthenticationManager</code> must honour the following contract concerning exceptions:</p>
     *  <p>A {@link DisabledException} must be thrown if an account is disabled and the
     * <code>AuthenticationManager</code> can test for this state.</p>
     *  <p>A {@link LockedException} must be thrown if an account is locked and the
     * <code>AuthenticationManager</code> can test for account locking.</p>
     *  <p>A {@link BadCredentialsException} must be thrown if incorrect credentials are presented. Whilst the
     * above exceptions are optional, an <code>AuthenticationManager</code> must <B>always</B> test credentials.</p>
     *  <p>Exceptions should be tested for and if applicable thrown in the order expressed above (ie if an
     * account is disabled or locked, the authentication request is immediately rejected and the credentials testing
     * process is not performed). This prevents credentials being tested against  disabled or locked accounts.</p>
     *
	 * @param IAuthentication $authentication
	 * @return IAuthentication
	 * 
	 * @throws AuthenticationException if authentication fails
	 */
	function authenticate(IAuthentication $authentication);
}