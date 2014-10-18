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

use Bee\Security\IPasswordEncoder;

class Bee_Security_PasswordEncoder_MD5 implements IPasswordEncoder {
	
    public function encodePassword($rawPass, $salt) {
    	return md5($rawPass);
    }
	
	public function isPasswordValid($encPass, $rawPass, $salt) {
		return $encPass === md5($rawPass);
	}
    
}