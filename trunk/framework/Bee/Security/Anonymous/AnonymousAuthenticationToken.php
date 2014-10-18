<?php
namespace Bee\Security\Anonymous;
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
use Bee\Security\AbstractAuthenticationToken;
use Bee\Security\IGrantedAuthority;
use Bee\Utils\Strings;
use InvalidArgumentException;

/**
 * Class AuthenticationToken
 * @package Bee\Security\Anonymous
 */
class AnonymousAuthenticationToken extends AbstractAuthenticationToken {

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
     * @param IGrantedAuthority[] $authorities the authorities granted to the principal
     */
    public function __construct($key, $principal, $authorities = null) {
        parent::__construct($authorities);

        if (!Strings::hasText($key) || !Strings::hasText($key) || !is_array($authorities)
                || count($authorities) == 0) {
            throw new InvalidArgumentException('Cannot pass null or empty values to constructor');
        }

        $this->keyHash = hash('md5', $key);
        $this->principal = $principal;
        $this->setAuthenticated(true);
    }

    //~ Methods ========================================================================================================

    public function equals($obj) {
        if ($obj instanceof AnonymousAuthenticationToken) {
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
     * @return string an empty String
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
        return 'AuthenticationToken['.$this->principal.','.$this->keyHash.']';
    }
}