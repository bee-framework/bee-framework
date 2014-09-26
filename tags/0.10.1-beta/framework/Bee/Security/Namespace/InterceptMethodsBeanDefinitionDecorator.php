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
use Bee\Context\Config\BeanDefinitionHolder;
use Bee\Context\Xml\ParserContext;
use Bee\Context\Xml\XmlNamespace\IBeanDefinitionDecorator;

/**
 * Created by IntelliJ IDEA.
 * User: mp
 * Date: Feb 19, 2010
 * Time: 4:34:35 PM
 * To change this template use File | Settings | File Templates.
 */

class Bee_Security_Namespace_InterceptMethodsBeanDefinitionDecorator implements IBeanDefinitionDecorator {

    function decorate(DOMNode $node, BeanDefinitionHolder $definition, ParserContext $parserContext) {
        // TODO: Implement decorate() method.
    }
}