<?php
namespace Bee\Context\Config;
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
 * User: mp
 * Date: Feb 18, 2010
 * Time: 7:11:18 AM
 */

class CompositeComponentDefinition extends AbstractComponentDefinition {

    private $name;

    private $nestedComponents = array();


    /**
     * Create a new CompositeComponentDefinition.
     * @param string $name the name of the composite component
     */
    public function __construct($name) {
        $this->name = $name;
    }


    public function getName() {
        return $this->name;
    }

    /**
     * Add the given component as nested element of this composite component.
     * @param IComponentDefinition $component the nested component to add
     */
    public function addNestedComponent(IComponentDefinition $component) {
        $this->nestedComponents[] = $component;
    }

    /**
     * Return the nested components that this composite component holds.
     * @return IComponentDefinition[] the array of nested components, or an empty array if none
     */
    public function getNestedComponents() {
        return $this->nestedComponents;
    }
}