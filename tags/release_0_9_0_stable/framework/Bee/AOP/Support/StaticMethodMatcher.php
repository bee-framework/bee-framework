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
 * Date: Feb 19, 2010
 * Time: 11:06:26 PM
 */

abstract class Bee_AOP_Support_StaticMethodMatcher implements Bee_AOP_IMethodMatcher {

    public final function isRuntime() {
        return false;
    }

    public final function matchesAtRuntime(ReflectionMethod $method, ReflectionClass $targetClass, array $args) {
        // should never be invoked because isRuntime() returns false
        throw new BadMethodCallException("Illegal MethodMatcher usage");
    }

}
