<?php
namespace Bee\Security\PasswordEncoder;
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

use Bee\Security\IPasswordEncoder;

class PlainTextEncoder implements IPasswordEncoder {

	/**
	 * @param string $rawPass the password to encode
	 * @param mixed $salt optionally used by the implementation to "salt" the raw password before encoding. A
     *        <code>null</code> value is legal.
     *
     * @return string encoded password
	 */
    public function encodePassword($rawPass, $salt) {
    	return $rawPass;
    }

	/**
	 * @param string $encPass a pre-encoded password
	 * @param string $rawPass a raw password to encode and compare against the pre-encoded password
	 * @param mixed $salt optionally used by the implementation to "salt" the raw password before encoding. A
	 *        <code>null</code> value is legal.
	 *
	 * @return boolean true if the password is valid , false otherwise
	 */
	public function isPasswordValid($encPass, $rawPass, $salt) {
		return $encPass === $rawPass;
	}	
}