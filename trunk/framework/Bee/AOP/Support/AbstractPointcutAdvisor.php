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
 * Time: 10:40:52 PM
 */

abstract class Bee_AOP_Support_AbstractPointcutAdvisor implements Bee_AOP_IPointcutAdvisor, Bee_Utils_IOrdered {

    /**
     * @var int
     */
    private $order = Bee_Utils_IOrdered::LOWEST_PRECEDENCE;

    /**
     * Gets the Order
     *
     * @return int $order
     */
    public function getOrder() {
        return $this->order;
    }

    /**
     * Sets the Order
     *
     * @param int $order
     * @return void
     */
    public function setOrder($order) {
        $this->order = $order;
    }
}
