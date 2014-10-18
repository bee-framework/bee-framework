<?php
namespace Bee\Security\Concurrent;
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
use Bee\Security\IAuthentication;

/**
 * Interface ISessionController
 * Provides two methods that can be called by an {@link IAuthenticationManager} to integrate with
 * the concurrent session handling infrastructure.
 * @package Bee\Security\Concurrent
 */
interface ISessionController {
	
	/**
	 * Called by any class that wishes to know whether the current authentication request should be permitted.
     * Generally callers will be <code>IAuthenticationManager</code>s before they authenticate, but
     * could equally include <code>Filter</code>s or other interceptors that wish to confirm the ongoing validity
     * of a previously authenticated <code>IAuthentication</code>.<p>The implementation should throw
     * a suitable exception if the user has exceeded their maximum allowed concurrent sessions.</p>
	 *
	 * @param IAuthentication $request the authentication request (never <code>null</code>)
	 * 
	 * @throws AuthenticationException if the user has exceeded their maximum allowed current sessions
	 * 
	 * @return void
	 */
    public function checkAuthenticationAllowed(IAuthentication $request);
	
    /**
     * Called by an <code>IAuthenticationManager</code> when the authentication was successful. An
     * implementation is expected to register the authenticated user in some sort of registry, for future concurrent
     * tracking via the {@link #checkAuthenticationAllowed(IAuthentication)} method.
     *
     * @param IAuthentication $authentication the successfully authenticated user (never <code>null</code>)
     * 
     * @return void
     */
    public function registerSuccessfulAuthentication(IAuthentication $authentication);
}