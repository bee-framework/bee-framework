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
use Bee\Context\Xml\XmlNamespace\HandlerSupport;

/**
 * User: mp
 * Date: Feb 17, 2010
 * Time: 6:35:32 PM
 */

class Bee_AOP_Namespace_Handler extends HandlerSupport {

    public function init() {
//        $this->registerBeanDefinitionParser("config", new ConfigBeanDefinitionParser());
        $this->registerBeanDefinitionDecorator("scoped-proxy", new Bee_AOP_Namespace_ScopedProxyBeanDefinitionDecorator());
    }
}