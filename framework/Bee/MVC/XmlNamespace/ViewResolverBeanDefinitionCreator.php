<?php
namespace Bee\MVC\XmlNamespace;
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
use Bee\Beans\PropertyValue;
use Bee\Context\Config\BeanDefinitionHolder;
use Bee\Context\Config\RuntimeBeanReference;
use Bee\Context\Support\BeanDefinitionReaderUtils;
use Bee\Context\Xml\ParserContext;
use Bee\Context\Xml\XmlNamespace\IBeanDefinitionParser;
use Bee_MVC_Dispatcher;
use DOMElement;

/**
 * Class ViewResolverBeanDefinitionCreator
 * @package Bee\MVC\XmlNamespace
 */
class ViewResolverBeanDefinitionCreator implements IBeanDefinitionParser {

	const DEFAULT_VIEW_CONTEXT_BEAN_NAME = '__viewContext';

	const CONTEXT_LOCATION_PROPERTY_NAME = 'contextLocation';

	/**
	 * Enter description here...
	 *
	 * @param DOMElement $element
	 * @param ParserContext $parserContext
	 * @return \Bee\Context\Config\IBeanDefinition
	 */
	function parse(DOMElement $element, ParserContext $parserContext) {
		$contextDefinition = BeanDefinitionReaderUtils::createBeanDefinition(null, 'Bee_Context_Xml');

		$contextLoc = $element->hasAttribute(self::CONTEXT_LOCATION_PROPERTY_NAME) ? $element->getAttribute(self::CONTEXT_LOCATION_PROPERTY_NAME) : false;
		if(!$contextLoc) {
			$contextLoc = 'conf/views.xml';
		}
		$contextDefinition->addConstructorArgumentValue(new PropertyValue(0, $contextLoc));

		$contextDefHolder = new
		BeanDefinitionHolder($contextDefinition, self::DEFAULT_VIEW_CONTEXT_BEAN_NAME);
		$parserContext->getDelegate()->decorateBeanDefinitionIfRequired($element, $contextDefHolder);
		BeanDefinitionReaderUtils::registerBeanDefinition($contextDefHolder, $parserContext->getRegistry());

		$resolverDefinition = BeanDefinitionReaderUtils::createBeanDefinition(null, 'Bee\MVC\ViewResolver\BasicViewResolver');
		$resolverDefinition->addPropertyValue(new PropertyValue('context', new RuntimeBeanReference(array(self::DEFAULT_VIEW_CONTEXT_BEAN_NAME))));
		$resolverDefHolder = new BeanDefinitionHolder($resolverDefinition, Bee_MVC_Dispatcher::VIEW_RESOLVER_BEAN_NAME);
		BeanDefinitionReaderUtils::registerBeanDefinition($resolverDefHolder, $parserContext->getRegistry());
	}
}
