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
 * Date: Feb 18, 2010
 * Time: 7:11:18 AM
 */

abstract class Bee_Context_Config_AbstractComponentDefinition implements Bee_Context_Config_IComponentDefinition {

    /**
     * Delegates to {@link #getName}.
     */
    public function getDescription() {
        return $this->getName();
    }

    /**
     * Returns an empty array.
     */
    public function getBeanDefinitions() {
        return array();
    }

    /**
     * Returns an empty array.
     */
    public function getInnerBeanDefinitions() {
        return array();
    }

    /**
     * Returns an empty array.
     */
    public function getBeanReferences() {
        return array();
    }

    /**
     * Delegates to {@link #getDescription}.
     */
    public function __toString() {
        return $this->getDescription();
    }
}

?>