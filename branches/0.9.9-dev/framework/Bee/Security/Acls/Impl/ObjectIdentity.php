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
use Bee\Security\Acls\IObjectIdentity;

/**
 * User: mp
 * Date: Mar 16, 2010
 * Time: 6:43:55 PM
 */

class ObjectIdentity implements IObjectIdentity {

    /**
     * @var string
     */
    private $type;

    /**
     * @var mixed
     */
    private $identifier;

    public function __construct($type, $identifier) {
        $this->type = $type;
        $this->identifier = $identifier;
    }

    /**
     * Gets the Identifier
     *
     * @return mixed $identifier
     */
    public function getIdentifier() {
        return $this->identifier;
    }

    /**
     * Gets the Type
     *
     * @return string $type
     */
    public function getType() {
        return $this->type;
    }

    public function getIdentifierString() {
        return $this->type . '#' . $this->identifier;
    }

    public function __toString() {
        return 'ObjectIdentity['.$this->getIdentifierString().']';
    }

	public function equals(IObjectIdentity $other) {
		return $this->getIdentifierString() == $other->getIdentifierString();
	}
}
