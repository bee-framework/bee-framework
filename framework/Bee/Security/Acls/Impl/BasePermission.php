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
use Bee\Security\Acls\FormattingUtils;
use Bee\Security\Acls\IPermission;

/**
 * Class BasePermission
 * @package Bee\Security\Acls\Impl
 */
class BasePermission implements IPermission {

	/**
	 * @var
	 */
    protected $code;

	/**
	 * @var
	 */
    protected $mask;

	/**
	 * @param $mask
	 * @param $code
	 */
    public function __construct($mask, $code) {
        $this->mask = $mask;
        $this->code = $code;
    }

	/**
	 * @return string
	 */
    public function getMask() {
        return $this->mask;
    }

	/**
	 * @return string
	 */
    public function getPattern() {
        return FormattingUtils::printActiveBinary($this->mask, $this->code);
    }

	/**
	 * @return string
	 */
    public function __toString() {
        return get_class($this).'['.$this->getPattern().'='.$this->mask.']';
    }

	/**
	 * @param IPermission $other
	 * @return bool
	 */
	public function equals(IPermission $other) {
		return $this->getMask() == $other->getMask();
	}
}
