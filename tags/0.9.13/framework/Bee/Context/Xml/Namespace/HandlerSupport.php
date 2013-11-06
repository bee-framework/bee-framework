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

/**
 * Support class for implementing custom {@link Bee_Context_Xml_Namespace_IHandler NamespaceHandlers}. Parsing and
 * decorating of individual {@link DOMNode Nodes} is done via {@link Bee_Context_Xml_Namespace_IBeanDefinitionParser} and
 * {@link Bee_Context_Xml_Namespace_IBeanDefinitionDecorator} strategy interfaces respectively.
 */
abstract class Bee_Context_Xml_Namespace_HandlerSupport implements Bee_Context_Xml_Namespace_IHandler {
	
	/**
	 * Enter description here...
	 *
	 * @var array
	 */
	private $parsers;
	
	/**
	 * Enter description here...
	 *
	 * @var array
	 */
	private $decorators;
	
	/**
	 * Enter description here...
	 *
	 * @var array
	 */
	private $attributeDecorators;
	
	public final function parse(DOMElement $element, Bee_Context_Xml_ParserContext $parserContext) {
		return $this->findParserForElement($element, $parserContext)->parse($element, $parserContext);
	}
	
	/**
	 * Locates the BeanDefinitionParser from the register implementations using
	 * the local name of the supplied DOMElement.
	 *
	 * @param DOMElement $element
	 * @param Bee_Context_Xml_ParserContext $parserContext
	 * @return Bee_Context_Xml_Namespace_IBeanDefinitionParser
	 */
	private function findParserForElement(DOMElement $element, Bee_Context_Xml_ParserContext $parserContext) {
		$parser = $this->parsers[$element->localName];
		if (is_null($parser)) {
			$parserContext->getReaderContext()->error('Cannot locate BeanDefinitionParser for element [' . $element->localName . ']', $element);
		}
		return $parser;
	}

	public final function decorate(DOMNode $source, Bee_Context_Config_BeanDefinitionHolder $definition, Bee_Context_Xml_ParserContext $parserContext) {
		return $this->findDecoratorForNode($source, $parserContext)->decorate($source, $definition, $parserContext);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param DOMNode $node
	 * @param Bee_Context_Xml_ParserContext $parserContext
	 * @return Bee_Context_Xml_Namespace_IBeanDefinitionDecorator
	 */
	private function findDecoratorForNode(DOMNode $node, Bee_Context_Xml_ParserContext $parserContext) {
		$decorator = null;
		if ($node instanceof DOMElement) {
			$decorator = $this->decorators[$node->localName];
		} else if ($node instanceof DOMAttr) {
			$decorator = $this->attributeDecorators[$node->localName];
		} else {
			$parserContext->getReaderContext()->error('Cannot decorate based on Nodes of type [' . get_class($node) . ']', $node);
		}
		if ($decorator == null) {
			$parserContext->getReaderContext()->error('Cannot locate BeanDefinitionDecorator for ' .
					($node instanceof DOMElement ? 'element' : 'attribute') . ' [' . $node->localName . ']', $node);
		}
		return $decorator;
	}
	
	/**
	 * Subclasses can call this to register the supplied {@link BeanDefinitionParser} to
	 * handle the specified element. The element name is the local (non-namespace qualified)
	 * name.
	 */
	protected final function registerBeanDefinitionParser($elementName, Bee_Context_Xml_Namespace_IBeanDefinitionParser $parser) {
		$this->parsers[$elementName] = $parser;
	}

	/**
	 * Subclasses can call this to register the supplied {@link BeanDefinitionDecorator} to
	 * handle the specified element. The element name is the local (non-namespace qualified)
	 * name.
	 */
	protected final function registerBeanDefinitionDecorator($elementName, Bee_Context_Xml_Namespace_IBeanDefinitionDecorator $decorator) {
		$this->decorators[$elementName] = $decorator;
	}

	/**
	 * Subclasses can call this to register the supplied {@link BeanDefinitionDecorator} to
	 * handle the specified attribute. The attribute name is the local (non-namespace qualified)
	 * name.
	 */
	protected final function registerBeanDefinitionDecoratorForAttribute($attributeName, Bee_Context_Xml_Namespace_IBeanDefinitionDecorator $decorator) {
		$this->attributeDecorators[$attributeName] = $decorator;
	}
	
}