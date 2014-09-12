<?php
namespace Bee\Context\Xml\XmlNamespace;
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
use Bee\Context\Config\IBeanDefinition;
use Bee\Context\Xml\ParserContext;
use DOMElement;
use DOMNode;

/**
 * Enter description here...
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
interface IHandler {

	function init();
	
	/**
	 * Enter description here...
	 *
	 * @param DOMElement $element
	 * @param ParserContext $parserContext
	 * @return IBeanDefinition
	 */
	function parse(DOMElement $element, ParserContext $parserContext);
	
	
	/**
	 * Enter description here...
	 *
	 * @param DOMNode $source
	 * @param BeanDefinitionHolder $definition
	 * @param ParserContext $parserContext
	 * @return BeanDefinitionHolder
	 */
	function decorate(DOMNode $source, BeanDefinitionHolder $definition, ParserContext $parserContext);
}