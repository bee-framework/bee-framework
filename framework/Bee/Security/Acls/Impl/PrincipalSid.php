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
 * User: mp
 * Date: Mar 17, 2010
 * Time: 11:24:30 AM
 */

class Bee_Security_Acls_Impl_PrincipalSid implements Bee_Security_Acls_ISid {

    /**
     * @var string
     */
    private $principal;

    /**
     * Gets the Principal
     *
     * @return string $principal
     */
    public function getPrincipal() {
        return $this->principal;
    }

    /**
     * @param string|Bee_Security_IAuthentication $principal
     * @return void
     */
    public function __construct($principalOrAuth) {
        Bee_Utils_Assert::notNull($principalOrAuth);
        if(is_string($principalOrAuth)) {
            $this->principal = $principalOrAuth;
        } else {
            Bee_Utils_Assert::isTrue($principalOrAuth instanceof Bee_Security_IAuthentication, 'Principal is neither string nor authentication object');
            $principal = $principalOrAuth->getPrincipal();
            if($principal instanceof Bee_Security_IUserDetails) {
                $this->principal = $principal->getUsername();
            } else {
                $this->principal = ''.$principal;
            }
        }
    }

    public function getIdentifierString() {
        return $this->getPrincipal();
    }

    public function __toString() {
        return 'Bee_Security_Acls_Impl_PrincipalSid['.$this->getIdentifierString().']';
    }

    public function equals(Bee_Security_Acls_ISid $sid) {
        return $this->getIdentifierString() == $sid->getIdentifierString();
    }
}
?>
