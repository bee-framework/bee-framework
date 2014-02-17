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
 * Provides two methods that can be called by an {@link Bee_Security_IAuthenticationManager} to integrate with
 * the concurrent session handling infrastructure.
 *
 */
interface Bee_Security_Concurrent_ISessionController {
	
	/**
	 * Called by any class that wishes to know whether the current authentication request should be permitted.
     * Generally callers will be <code>Bee_Security_IAuthenticationManager</code>s before they authenticate, but
     * could equally include <code>Filter</code>s or other interceptors that wish to confirm the ongoing validity
     * of a previously authenticated <code>Bee_Security_IAuthentication</code>.<p>The implementation should throw
     * a suitable exception if the user has exceeded their maximum allowed concurrent sessions.</p>
	 *
	 * @param Bee_Security_IAuthentication $request the authentication request (never <code>null</code>)
	 * 
	 * @throws Bee_Security_Exception_Authentication if the user has exceeded their maximum allowed current sessions
	 * 
	 * @return void
	 */
    public function checkAuthenticationAllowed(Bee_Security_IAuthentication $request);
	
    /**
     * Called by an <code>Bee_Security_IAuthenticationManager</code> when the authentication was successful. An
     * implementation is expected to register the authenticated user in some sort of registry, for future concurrent
     * tracking via the {@link #checkAuthenticationAllowed(Bee_Security_IAuthentication)} method.
     *
     * @param Bee_Security_IAuthentication $authentication the successfully authenticated user (never <code>null</code>)
     * 
     * @return void
     */
    public function registerSuccessfulAuthentication(Bee_Security_IAuthentication $authentication);
}
?>