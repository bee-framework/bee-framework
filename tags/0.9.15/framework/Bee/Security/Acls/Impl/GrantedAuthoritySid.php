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
 * Time: 11:36:06 AM
 */

class Bee_Security_Acls_Impl_GrantedAuthoritySid implements Bee_Security_Acls_ISid {

    /**
     * @var string
     */
    private $grantedAuthority;

    /**
     * @param string|Bee_Security_IGrantedAuthority $grantedAuthority
     * @return void
     */
    public function __construct($grantedAuthority) {
        Bee_Utils_Assert::notNull($grantedAuthority);
        if(is_string($grantedAuthority)) {
            $this->grantedAuthority = $grantedAuthority;
        } else {
            Bee_Utils_Assert::isTrue($grantedAuthority instanceof Bee_Security_IGrantedAuthority, 'Unknown granted authority type');
            $this->grantedAuthority = $grantedAuthority->getAuthority();
        }
    }

    /**
     * Gets the GrantedAuthority
     *
     * @return string $grantedAuthority
     */
    public function getGrantedAuthority() {
        return $this->grantedAuthority;
    }

    public function getIdentifierString() {
        return $this->grantedAuthority;
    }

    public function __toString() {
        return 'GrantedAuthoritySid['.$this->grantedAuthority.']';
    }

    public function equals(Bee_Security_Acls_ISid $sid) {
        return $this->getIdentifierString() == $sid->getIdentifierString();
    }
}
?>
