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

use Bee\Utils\Assert;

abstract class Bee_Security_PasswordEncoder_Base implements Bee_Security_IPasswordEncoder {
	
	/**
	 * Enter description here...
	 *
	 * @param String $mergedPasswordSalt
	 * 
	 * @return array<String>(2)
	 */
    protected function demergePasswordAndSalt($mergedPasswordSalt) {
		Assert::hasText($mergedPasswordSalt, 'Cannot pass a null or empty String');

        $password = $mergedPasswordSalt;
        $salt = '';

        $saltBegins = strrpos($mergedPasswordSalt, '{');

        $mergedLen = strlen($mergedPasswordSalt);
        if (($saltBegins != -1) && (($saltBegins + 1) < $mergedLen)) {
            $salt = substr($mergedPasswordSalt, $saltBegins + 1, $mergedLen - 1);
            $password = substr($mergedPasswordSalt, 0, $saltBegins);
        }

        return array($password, $salt);
    }
	
    /**
     * Enter description here...
     *
     * @param String $password
     * @param mixed $salt
     * @param boolean $strict
     * 
     * @return String
     */
    protected function mergePasswordAndSalt($password, $salt, $strict) {
        if (is_null($password)) {
            $password = "";
        }

        if ($strict && !is_null($salt)) {
            if ((salt.toString().lastIndexOf("{") != -1) || (salt.toString().lastIndexOf("}") != -1)) {
                throw new IllegalArgumentException("Cannot use { or } in salt.toString()");
            }
        }

        if ((salt == null) || "".equals(salt)) {
            return password;
        } else {
            return password + "{" + salt.toString() + "}";
        }
    }
    
}