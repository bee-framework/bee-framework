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
 * Date: Mar 23, 2010
 * Time: 9:52:56 AM
 */

class Bee_Security_Acls_Impl_CumulativePermission extends Bee_Security_Acls_Impl_BasePermission {

    private $pattern = self::THIRTY_TWO_RESERVED_OFF;

    public function __construct() {
        parent::__construct(0, ' ');
    }

    public function set(Bee_Security_Acls_IPermission $permission) {
        $this->mask |= $permission->getMask();
        $this->pattern = Bee_Security_Acls_FormattingUtils::mergePatterns($this->pattern, $permission->getPattern());
        return $this;
    }
    public function getPattern() {
        return $this->pattern;
    }

}
?>
