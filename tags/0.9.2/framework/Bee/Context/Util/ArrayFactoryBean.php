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
 * Date: 01.07.11
 * Time: 04:48
 */
 
class Bee_Context_Util_ArrayFactoryBean extends Bee_Context_Util_AbstractFactoryBean {

    private $sourceArray;

    public function setSourceArray(array $sourceArray) {
        $this->sourceArray = &$sourceArray;
    }

    public function isSingleton() {
        return true;
    }

    protected function &createInstance() {
        if($this->isSingleton()) {
            return $this->sourceArray;
        }
        $copy = $this->sourceArray;
        return $copy;
    }

    function getObjectType() {
        return null;
    }
}
