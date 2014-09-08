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
 * Defines an interface for implementations that wish to provide data access
 * services to the {@link DaoAuthenticationProvider}.
 *
 * <p>
 * The interface requires only one read-only method, which simplifies support
 * of new data access strategies.
 * </p>
 */
interface Bee_Security_IUserDetailsService {

	/**
	 * Locates the user based on the username. In the actual implementation, the search may possibly be case
	 * insensitive, or case insensitive depending on how the implementaion instance is configured. In this case, the
	 * <code>Bee_Security_IUserDetails</code> object that comes back may have a username that is of a different case
	 * than what was actually requested.
	 *
	 * @param $username
	 * @return Bee_Security_IUserDetails a fully populated user record (never <code>null</code>)
	 *
	 */
	function loadUserByUsername($username);
}