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
 * Time: 8:52:33 PM
 * To change this template use File | Settings | File Templates.
 */

class Bee_Security_Anonymous_AuthenticationToken extends Bee_Security_AbstractAuthenticationToken {

    /**
     * @var mixed
     */
    private $principal;

    /**
     * @var string
     */
    private $keyHash;

    //~ Constructors ===================================================================================================

    /**
     * Constructor.
     *
     * @param string $key to identify if this object made by an authorised client
     * @param mixed $principal the principal (typically a <code>UserDetails</code>)
     * @param Bee_Security_IGrantedAuthority[] $authorities the authorities granted to the principal
     *
     * @throws IllegalArgumentException if a <code>null</code> was passed
     */
    public function __construct($key, $principal, $authorities = null) {
        parent::__construct($authorities);

        if (!Bee_Utils_Strings::hasText($key) || !Bee_Utils_Strings::hasText($key) || !is_array($authorities)
                || count($authorities) == 0) {
            throw new InvalidArgumentException('Cannot pass null or empty values to constructor');
        }

        $this->keyHash = hash('md5', $key);
        $this->principal = $principal;
        $this->setAuthenticated(true);
    }

    //~ Methods ========================================================================================================

    public function equals($obj) {
        if ($obj instanceof Bee_Security_Anonymous_AuthenticationToken) {
            if ($this->getKeyHash() != $obj->getKeyHash()) {
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * Always returns an empty <code>String</code>
     *
     * @return an empty String
     */
    public function getCredentials() {
        return '';
    }

    public function getKeyHash() {
        return $this->keyHash;
    }

    public function getPrincipal() {
        return $this->principal;
    }

    public function __toString() {
        return 'Bee_Security_Anonymous_AuthenticationToken['.$this->principal.','.$this->keyHash.']';
    }
}
