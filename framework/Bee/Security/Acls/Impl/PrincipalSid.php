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
use Bee\Security\IAuthentication;
use Bee\Security\IUserDetails;
use Bee\Utils\Assert;

/**
 * Class PrincipalSid
 * @package Bee\Security\Acls\Impl
 */
class PrincipalSid implements ISid {

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
	 * @param IAuthentication|string $principalOrAuth
	 * @return PrincipalSid
	 */
    public function __construct($principalOrAuth) {
		Assert::notNull($principalOrAuth);
        if(is_string($principalOrAuth)) {
            $this->principal = $principalOrAuth;
        } else {
			Assert::isTrue($principalOrAuth instanceof IAuthentication, 'Principal is neither string nor authentication object');
            $principal = $principalOrAuth->getPrincipal();
            if($principal instanceof IUserDetails) {
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
        return 'PrincipalSid['.$this->getIdentifierString().']';
    }

    public function equals(ISid $sid) {
        return $this->getIdentifierString() == $sid->getIdentifierString();
    }
}

