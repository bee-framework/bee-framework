<?php
namespace Bee\Context\Util\XmlNamespace;
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
use Bee\Context\Xml\XmlNamespace\HandlerSupport;

/**
 * Class Handler
 * @package Bee\Context\Util\XmlNamespace
 */
class Handler extends HandlerSupport {

    public function init() {
        $this->registerBeanDefinitionParser("array", new ArrayFactoryDefinitionParser());
        $this->registerBeanDefinitionParser("value", new ValueFactoryDefinitionParser());
    }
}
