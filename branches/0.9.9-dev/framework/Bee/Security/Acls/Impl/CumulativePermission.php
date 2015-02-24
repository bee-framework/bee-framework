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
 * Class CumulativePermission
 * @package Bee\Security\Acls\Impl
 */
class CumulativePermission extends BasePermission {

    private $pattern = self::THIRTY_TWO_RESERVED_OFF;

	/**
	 *
	 */
    public function __construct() {
        parent::__construct(0, ' ');
    }

	/**
	 * @param IPermission $permission
	 * @return $this
	 */
    public function set(IPermission $permission) {
        $this->mask |= $permission->getMask();
        $this->pattern = FormattingUtils::mergePatterns($this->pattern, $permission->getPattern());
        return $this;
    }

	/**
	 * @return string
	 */
    public function getPattern() {
        return $this->pattern;
    }
}
