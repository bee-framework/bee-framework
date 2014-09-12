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
use DOMAttr;
use DOMElement;
use DOMNode;

/**
 * Support class for implementing custom {@link IHandler NamespaceHandlers}. Parsing and
 * decorating of individual {@link DOMNode Nodes} is done via {@link IBeanDefinitionParser} and
 * {@link IBeanDefinitionDecorator} strategy interfaces respectively.
 */
abstract class HandlerSupport implements IHandler {
	
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

	/**
	 * @param DOMElement $element
	 * @param ParserContext $parserContext
	 * @return IBeanDefinition
	 */
	public final function parse(DOMElement $element, ParserContext $parserContext) {
		return $this->findParserForElement($element, $parserContext)->parse($element, $parserContext);
	}
	
	/**
	 * Locates the BeanDefinitionParser from the register implementations using
	 * the local name of the supplied DOMElement.
	 *
	 * @param DOMElement $element
	 * @param ParserContext $parserContext
	 * @return IBeanDefinitionParser
	 */
	private function findParserForElement(DOMElement $element, ParserContext $parserContext) {
		$parser = $this->parsers[$element->localName];
		if (is_null($parser)) {
			$parserContext->getReaderContext()->error('Cannot locate BeanDefinitionParser for element [' . $element->localName . ']', $element);
		}
		return $parser;
	}

	/**
	 * @param DOMNode $source
	 * @param BeanDefinitionHolder $definition
	 * @param ParserContext $parserContext
	 * @return BeanDefinitionHolder
	 */
	public final function decorate(DOMNode $source, BeanDefinitionHolder $definition, ParserContext $parserContext) {
		return $this->findDecoratorForNode($source, $parserContext)->decorate($source, $definition, $parserContext);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param DOMNode $node
	 * @param ParserContext $parserContext
	 * @return IBeanDefinitionDecorator
	 */
	private function findDecoratorForNode(DOMNode $node, ParserContext $parserContext) {
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
	 * @param $elementName
	 * @param IBeanDefinitionParser $parser
	 */
	protected final function registerBeanDefinitionParser($elementName, IBeanDefinitionParser $parser) {
		$this->parsers[$elementName] = $parser;
	}

	/**
	 * Subclasses can call this to register the supplied {@link BeanDefinitionDecorator} to
	 * handle the specified element. The element name is the local (non-namespace qualified)
	 * name.
	 * @param $elementName
	 * @param IBeanDefinitionDecorator $decorator
	 */
	protected final function registerBeanDefinitionDecorator($elementName, IBeanDefinitionDecorator $decorator) {
		$this->decorators[$elementName] = $decorator;
	}

	/**
	 * Subclasses can call this to register the supplied {@link BeanDefinitionDecorator} to
	 * handle the specified attribute. The attribute name is the local (non-namespace qualified)
	 * name.
	 * @param $attributeName
	 * @param IBeanDefinitionDecorator $decorator
	 */
	protected final function registerBeanDefinitionDecoratorForAttribute($attributeName, IBeanDefinitionDecorator $decorator) {
		$this->attributeDecorators[$attributeName] = $decorator;
	}
}