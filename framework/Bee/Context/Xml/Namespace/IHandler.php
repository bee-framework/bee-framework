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
use Bee\Context\Config\BeanDefinitionHolder;

/**
 * Enter description here...
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
interface Bee_Context_Xml_Namespace_IHandler {

	function init();
	
	/**
	 * Enter description here...
	 *
	 * @param DOMElement $element
	 * @param Bee_Context_Xml_ParserContext $parserContext
	 * @return Bee\Context\Config\IBeanDefinition
	 */
	function parse(DOMElement $element, Bee_Context_Xml_ParserContext $parserContext);
	
	
	/**
	 * Enter description here...
	 *
	 * @param DOMNode $source
	 * @param BeanDefinitionHolder $definition
	 * @param Bee_Context_Xml_ParserContext $parserContext
	 * @return BeanDefinitionHolder
	 */
	function decorate(DOMNode $source, BeanDefinitionHolder $definition, Bee_Context_Xml_ParserContext $parserContext);
}