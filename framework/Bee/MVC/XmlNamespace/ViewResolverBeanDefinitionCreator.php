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
use Bee_Context_Config_BeanDefinitionHolder;
use Bee_Context_Config_IBeanDefinition;
use Bee_Context_Config_RuntimeBeanReference;
use Bee_Context_Support_BeanDefinitionReaderUtils;
use Bee_Context_Xml_ParserContext;
use Bee_MVC_Dispatcher;
use DOMElement;

/**
 * Class ViewResolverBeanDefinitionCreator
 * @package Bee\MVC\XmlNamespace
 */
class ViewResolverBeanDefinitionCreator implements \Bee_Context_Xml_Namespace_IBeanDefinitionParser {

	const DEFAULT_VIEW_CONTEXT_BEAN_NAME = '__viewContext';

	const CONTEXT_LOCATION_PROPERTY_NAME = 'contextLocation';

	/**
	 * Enter description here...
	 *
	 * @param DOMElement $element
	 * @param Bee_Context_Xml_ParserContext $parserContext
	 * @return Bee_Context_Config_IBeanDefinition
	 */
	function parse(DOMElement $element, Bee_Context_Xml_ParserContext $parserContext) {
		$contextDefinition = Bee_Context_Support_BeanDefinitionReaderUtils::createBeanDefinition(null, 'Bee_Context_Xml');

		$contextLoc = $element->hasAttribute(self::CONTEXT_LOCATION_PROPERTY_NAME) ? $element->getAttribute(self::CONTEXT_LOCATION_PROPERTY_NAME) : false;
		if(!$contextLoc) {
			$contextLoc = 'conf/views.xml';
		}
		$contextDefinition->addConstructorArgumentValue(new PropertyValue(0, $contextLoc));

		$contextDefHolder = new Bee_Context_Config_BeanDefinitionHolder($contextDefinition, self::DEFAULT_VIEW_CONTEXT_BEAN_NAME);
		$parserContext->getDelegate()->decorateBeanDefinitionIfRequired($element, $contextDefHolder);
		Bee_Context_Support_BeanDefinitionReaderUtils::registerBeanDefinition($contextDefHolder, $parserContext->getRegistry());

		$resolverDefinition = Bee_Context_Support_BeanDefinitionReaderUtils::createBeanDefinition(null, 'Bee_MVC_ViewResolver_Basic');
		$resolverDefinition->addPropertyValue(new PropertyValue('context', new Bee_Context_Config_RuntimeBeanReference(array(self::DEFAULT_VIEW_CONTEXT_BEAN_NAME))));
		$resolverDefHolder = new Bee_Context_Config_BeanDefinitionHolder($resolverDefinition, Bee_MVC_Dispatcher::VIEW_RESOLVER_BEAN_NAME);
		Bee_Context_Support_BeanDefinitionReaderUtils::registerBeanDefinition($resolverDefHolder, $parserContext->getRegistry());
	}
}
