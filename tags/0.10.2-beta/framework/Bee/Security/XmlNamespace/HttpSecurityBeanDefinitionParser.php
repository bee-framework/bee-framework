<?php
namespace Bee\Security\XmlNamespace;
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
use Bee\Context\Xml\ParserContext;
use Bee\Context\Xml\XmlNamespace\IBeanDefinitionParser;
use DOMElement;

/**
 * Class HttpSecurityBeanDefinitionParser
 * @package Bee\Security
 */
class HttpSecurityBeanDefinitionParser implements IBeanDefinitionParser {
	
	public function parse(DOMElement $element, ParserContext $parserContext) {
		trigger_error('Bee\Security\HttpSecurityBeanDefinitionParser.parse() : TO BE IMPLEMENTED', E_USER_ERROR);
	}
}