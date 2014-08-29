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
 * Created by IntelliJ IDEA.
 * User: mp
 * Date: Apr 23, 2010
 * Time: 9:10:57 PM
 * To change this template use File | Settings | File Templates.
 */
class Bee_Security_Provider_AnonymousAuthentication implements Bee_Security_IAuthenticationProvider {

    private $key;

    //~ Methods ========================================================================================================

    public function authenticate(Bee_Security_IAuthentication $authentication) {
        if (!$this->supports($authentication)) {
            return null;
        }
		/** @var Bee_Security_AnonymousAuthenticationToken $authentication */

        if (hash('md5', $this->key) != $authentication->getKeyHash()) {
            throw new Bee_Security_Exception_BadCredentials('The presented AnonymousAuthenticationToken does not contain the expected key');
        }

        return $authentication;
    }

    public function getKey() {
        return $this->key;
    }

    public function setKey($key) {
        $this->key = $key;
    }

    public function supports($authenticationClass) {
        return Bee_Utils_Types::isAssignable($authenticationClass, 'Bee_Security_AnonymousAuthenticationToken');
    }
}
