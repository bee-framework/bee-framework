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
 * Time: 3:49:36 PM
 */

interface Bee_AOP_IMethodMatcher {

    /**
     * Perform static checking whether the given method matches. If this
     * returns <code>false</code> or if the {@link #isRuntime()} method
     * returns <code>false</code>, no runtime check (i.e. no.
     * {@link #matches(java.lang.reflect.Method, Class, Object[])} call) will be made.
     * @param ReflectionMethod $method the candidate method
     * @param ReflectionClass $targetClass the target class (may be <code>null</code>, in which case
     * the candidate class must be taken to be the method's declaring class)
     * @return boolean whether or not this method matches statically
     */
    public function matches(ReflectionMethod $method, ReflectionClass $targetClass);

    /**
     * Is this MethodMatcher dynamic, that is, must a final call be made on the
     * {@link #matches(java.lang.reflect.Method, Class, Object[])} method at
     * runtime even if the 2-arg matches method returns <code>true</code>?
     * <p>Can be invoked when an AOP proxy is created, and need not be invoked
     * again before each method invocation,
     * @return boolean whether or not a runtime match via the 3-arg
     * {@link #matches(java.lang.reflect.Method, Class, Object[])} method
     * is required if static matching passed
     */
    public function isRuntime();

    /**
     * Check whether there a runtime (dynamic) match for this method,
     * which must have matched statically.
     * <p>This method is invoked only if the 2-arg matches method returns
     * <code>true</code> for the given method and target class, and if the
     * {@link #isRuntime()} method returns <code>true</code>. Invoked
     * immediately before potential running of the advice, after any
     * advice earlier in the advice chain has run.
     * @param ReflectionMethod $method the candidate method
     * @param ReflectionClass $targetClass the target class (may be <code>null</code>, in which case
     * the candidate class must be taken to be the method's declaring class)
     * @param array $args arguments to the method
     * @return boolean whether there's a runtime match
     * @see MethodMatcher#matches(Method, Class)
     */
    public function matchesAtRuntime(ReflectionMethod $method, ReflectionClass $targetClass, array $args);


    /**
     * Canonical instance that matches all methods.
     */
//    MethodMatcher TRUE = TrueMethodMatcher.INSTANCE;
}

?>