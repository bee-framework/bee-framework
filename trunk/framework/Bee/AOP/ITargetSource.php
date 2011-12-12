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
 * Time: 12:40:15 AM
 */

interface Bee_AOP_ITargetSource extends Bee_AOP_ITargetClassAware {

    /**
     * Return the type of targets returned by this {@link TargetSource}.
     * <p>Can return <code>null</code>, although certain usages of a
     * <code>TargetSource</code> might just work with a predetermined
     * target class.
     * @return string the type of targets returned by this {@link TargetSource}
     */
    public function getTargetClassName();

    /**
     * Will all calls to {@link #getTarget()} return the same object?
     * <p>In that case, there will be no need to invoke
     * {@link #releaseTarget(Object)}, and the AOP framework can cache
     * the return value of {@link #getTarget()}.
     * @return boolean <code>true</code> if the target is immutable
     * @see #getTarget
     */
    function isStatic();

    /**
     * Return a target instance. Invoked immediately before the
     * AOP framework calls the "target" of an AOP method invocation.
     * @return mixed the target object, which contains the joinpoint
     * @throws Exception if the target object can't be resolved
     */
    public function getTarget();

    /**
     * Release the given target object obtained from the
     * {@link #getTarget()} method.
     * @param mixed $target object obtained from a call to {@link #getTarget()}
     * @return void
     * @throws Exception if the object can't be released
     */
    public function releaseTarget($target);

}

?>