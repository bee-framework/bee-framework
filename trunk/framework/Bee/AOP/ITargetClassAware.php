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
 * Minimal interface for exposing the target class behind a proxy.
 *
 * <p>Implemented by AOP proxy objects and proxy factories
 * (via {@link org.springframework.aop.framework.Advised}}
 * as well as by {@link TargetSource TargetSources}.
 *
 * User: mp
 * Date: Feb 17, 2010
 * Time: 9:10:32 PM
 * To change this template use File | Settings | File Templates.
 */

interface Bee_AOP_ITargetClassAware {
    /**
     * Return the target class name behind the implementing object (typically a proxy configuration or an actual proxy).
     *
     * @return string the target class name, or <code>null</code> if not known
     */
    public function getTargetClassName();
}

?>