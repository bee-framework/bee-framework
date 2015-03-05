<?php
namespace Bee\Security\Provider;
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
use Bee\Security\IUserDetails;

/**
 * Class UsernameSaltSource
 *
 * Provides alternative sources of the salt to use for encoding passwords.
 * @package Bee\Security\Provider
 */
class UsernameSaltSource implements ISaltSource {
	/**
	 * Returns the salt to use for the indicated user.
	 *
	 * @param IUserDetails $user from the <code>AuthenticationDao</code>
	 * @return String the salt to use for this <code>IUserDetails</code>
	 */
    public function getSalt(IUserDetails $user) {
        return $user->getUsername();
    }
}