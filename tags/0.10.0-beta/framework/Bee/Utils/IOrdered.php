<?php
/*
 * Copyright 2008-2014 the original author or authors.
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
 * Created by IntelliJ IDEA.
 * User: mp
 * Date: Feb 19, 2010
 * Time: 10:38:44 PM
 * To change this template use File | Settings | File Templates.
 */
interface Bee_Utils_IOrdered {

    /**
     * Useful constant for the highest precedence value.
     * @see java.lang.Integer#MIN_VALUE
     */
    const HIGHEST_PRECEDENCE = -10000;

    /**
     * Useful constant for the lowest precedence value.
     * @see java.lang.Integer#MAX_VALUE
     */
    const LOWEST_PRECEDENCE = 10000;


    /**
     * Return the order value of this object, with a
     * higher value meaning greater in terms of sorting.
     * <p>Normally starting with 0 or 1, with {@link #LOWEST_PRECEDENCE}
     * indicating greatest. Same order values will result in arbitrary
     * positions for the affected objects.
     * <p>Higher value can be interpreted as lower priority,
     * consequently the first object has highest priority
     * (somewhat analogous to Servlet "load-on-startup" values).
     * <p>Note that order values below 0 are reserved for framework
     * purposes. Application-specified values should always be 0 or
     * greater, with only framework components (internal or third-party)
     * supposed to use lower values.
     * @return the order value
     * @see #LOWEST_PRECEDENCE
     */
    public function getOrder();
}