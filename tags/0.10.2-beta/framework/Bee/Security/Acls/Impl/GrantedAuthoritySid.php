<?php
namespace Bee\Security\Acls\Impl;
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
use Bee\Security\Acls\ISid;
use Bee\Security\IGrantedAuthority;
use Bee\Utils\Assert;

/**
 * Class GrantedAuthoritySid
 * @package Bee\Security\Acls\Impl
 */
class GrantedAuthoritySid implements ISid {

    /**
     * @var string
     */
    private $grantedAuthority;

	/**
	 * @param string|IGrantedAuthority $grantedAuthority
	 * @return GrantedAuthoritySid
	 */
    public function __construct($grantedAuthority) {
		Assert::notNull($grantedAuthority);
        if(is_string($grantedAuthority)) {
            $this->grantedAuthority = $grantedAuthority;
        } else {
			Assert::isTrue($grantedAuthority instanceof IGrantedAuthority, 'Unknown granted authority type');
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

    public function equals(ISid $sid) {
        return $this->getIdentifierString() == $sid->getIdentifierString();
    }
}