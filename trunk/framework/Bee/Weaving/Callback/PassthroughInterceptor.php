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
 * Date: 05.07.11
 * Time: 01:56
 */
 
class Bee_Weaving_Callback_PassthroughInterceptor implements Bee_Weaving_Callback_IMethodInterceptor {
    function intercept($object, $methodName, Bee_Weaving_Callback_IMethod $method, array $args = null) {
        return $method->invoke($args);
    }
}
