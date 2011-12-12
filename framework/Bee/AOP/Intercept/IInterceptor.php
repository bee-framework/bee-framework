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
 * This interface represents a generic interceptor.
 *
 * A generic interceptor can intercept runtime events that occur within a base program. Those events are materialized by
 * (reified in) joinpoints. Runtime joinpoints can be invocations, field access, exceptions...
 * 
 * User: mp
 * Date: Feb 17, 2010
 * Time: 4:43:48 PM
 */

interface Bee_AOP_Intercept_IInterceptor extends Bee_AOP_IAdvice {

}

?>