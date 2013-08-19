<?php
namespace Bee\MVC\XmlNamespace;

use Bee_Beans_PropertyValue;
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
		$contextDefinition->addConstructorArgumentValue(new Bee_Beans_PropertyValue(0, $contextLoc));

		$contextDefHolder = new Bee_Context_Config_BeanDefinitionHolder($contextDefinition, self::DEFAULT_VIEW_CONTEXT_BEAN_NAME);
		$parserContext->getDelegate()->decorateBeanDefinitionIfRequired($element, $contextDefHolder);
		Bee_Context_Support_BeanDefinitionReaderUtils::registerBeanDefinition($contextDefHolder, $parserContext->getRegistry());

		$resolverDefinition = Bee_Context_Support_BeanDefinitionReaderUtils::createBeanDefinition(null, 'Bee_MVC_ViewResolver_Basic');
		$resolverDefinition->addPropertyValue(new Bee_Beans_PropertyValue('context', new Bee_Context_Config_RuntimeBeanReference(array(self::DEFAULT_VIEW_CONTEXT_BEAN_NAME))));
		$resolverDefHolder = new Bee_Context_Config_BeanDefinitionHolder($contextDefinition, Bee_MVC_Dispatcher::VIEW_RESOLVER_BEAN_NAME);
		Bee_Context_Support_BeanDefinitionReaderUtils::registerBeanDefinition($resolverDefHolder, $parserContext->getRegistry());
	}
}
