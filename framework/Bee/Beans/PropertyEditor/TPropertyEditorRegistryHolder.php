<?php
/*
 * Copyright 2008-2015 the original author or authors.
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
namespace Bee\Beans\PropertyEditor;

use Bee\Beans\IPropertyEditor;
use Bee\IContext;

/**
 * Trait TPropertyEditorRegistryHolder
 * @package Bee\Beans\PropertyEditor
 */
trait TPropertyEditorRegistryHolder {

    /**
     * @var PropertyEditorRegistry
     */
    private $propertyEditorRegistry;

    /**
     * @param IContext $context
     */
    public function setBeeContext(IContext $context) {
        $this->propertyEditorRegistry = new PropertyEditorRegistry($context);
    }

    /**
     * @return PropertyEditorRegistry
     */
    public function getPropertyEditorRegistry() {
        return $this->propertyEditorRegistry;
    }

    /**
     * @param string $type
     * @return IPropertyEditor
     */
    public function getPropertyEditorForType($type) {
        return $this->propertyEditorRegistry->getEditor($type);
    }
}