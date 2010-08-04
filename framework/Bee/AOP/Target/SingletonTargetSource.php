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
 * Date: Feb 20, 2010
 * Time: 12:46:14 AM
 */

class Bee_AOP_Target_SingletonTargetSource implements Bee_AOP_ITargetSource {

    /** Target cached and invoked using reflection */
    private final $target;


    /**
     * Create a new SingletonTargetSource for the given target.
     * @param target the target object
     */
    public function __construct($target) {
        Bee_Utils_Assert::notNull($target, "Target object must not be null");
        $this->target = $target;
    }

    public function getTargetClassName() {
        return get_class($this->target);
    }

    public function getTarget() {
        return $this->target;
    }

    public function releaseTarget($target) {
        // nothing to do
    }

    public function isStatic() {
        return true;
    }

}
