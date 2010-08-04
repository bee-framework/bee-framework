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
 * Date: Mar 18, 2010
 * Time: 1:40:02 AM
 */

class Bee_Security_Acls_Impl_BasePermission implements Bee_Security_Acls_IPermission {

    protected $code;

    protected $mask;

    public function __construct($mask, $code) {
        $this->mask = $mask;
        $this->code = $code;
    }

    public function getMask() {
        return $this->mask;
    }

    public function getPattern() {
        return Bee_Security_Acls_FormattingUtils::printActiveBinary($this->mask, $this->code);
    }

    public function __toString() {
        return get_class($this).'['.$this->getPattern().'='.$this->mask.']';
    }
}
