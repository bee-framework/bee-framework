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
 * Enter description here...
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class Bee_Context_Xml_BeanDefinitionDocumentReader {
	
	const BEAN_ELEMENT = Bee_Context_Xml_ParserDelegate::BEAN_ELEMENT;
	
	const IMPORT_ELEMENT = 'import';
	
	const ALIAS_ELEMENT = 'alias';
		
	const NAME_ATTRIBUTE = 'name';

	const ALIAS_ATTRIBUTE = 'alias';
	
	/**
	 * Enter description here...
	 *
	 * @var Bee_Context_Xml_ReaderContext
	 */
	private $readerContext;
	
	public function registerBeanDefinitions(DOMDocument $doc, Bee_Context_Xml_ReaderContext $readerContext) {
		$this->readerContext = $readerContext;

		$root = $doc->documentElement;
		$delegate = $this->createHelper($root, $readerContext);
		$this->preProcessXml($root);
		$this->parseBeanDefinitions($root, $delegate);
		$this->postProcessXml($root);
	}

	/**
	 * Enter description here...
	 *
	 * @return Bee_Context_Xml_ReaderContext
	 */
	public function getReaderContext() {
		return $this->readerContext;		
	}
	
	/**
	 * Enter description here...
	 *
	 * @return Bee_Context_Xml_ParserDelegate
	 */
	protected function createHelper(DOMElement $root, Bee_Context_Xml_ReaderContext $readerContext) {
		$delegate = new Bee_Context_Xml_ParserDelegate($readerContext);
		$delegate->initDefaults($root);
		return $delegate;
	}

	/**
	 * Parse the elements at the root level in the document:
	 * "import", "alias", "bean".
	 * @param root the DOM root element of the document
	 */
	protected function parseBeanDefinitions(DOMElement $root, Bee_Context_Xml_ParserDelegate $delegate) {
		if ($delegate->isDefaultNamespace($root->namespaceURI)) {
			$nl = $root->childNodes;
			foreach($nl as $node) {
				if ($node instanceof DOMElement) {
					if ($delegate->isDefaultNamespace($node->namespaceURI)) {
						$this->parseDefaultElement($node, $delegate);
					} else {
						$delegate->parseCustomElement($node);
					}
				}
			}
		} else {
			$delegate->parseCustomElement($root);
		}
	}
	
	private function parseDefaultElement(DOMElement $ele, Bee_Context_Xml_ParserDelegate $delegate) {
		if (Bee_Utils_Dom::nodeNameEquals($ele, self::IMPORT_ELEMENT)) {
			$this->importBeanDefinitionResource($ele);
		} else if (Bee_Utils_Dom::nodeNameEquals($ele, self::ALIAS_ELEMENT)) {
			$this->processAliasRegistration($ele);
		} else if (Bee_Utils_Dom::nodeNameEquals($ele, self::BEAN_ELEMENT)) {
			$this->processBeanDefinition($ele, $delegate);
		}
	}
	
	protected function processAliasRegistration(DOMElement $ele) {
		$name = $ele->getAttribute(self::NAME_ATTRIBUTE);
		$alias = $ele->getAttribute(self::ALIAS_ATTRIBUTE);
		$valid = true;
		if (!Bee_Utils_Strings::hasText($name)) {
			$this->getReaderContext()->error('Name must not be empty', $ele);
			$valid = false;
		}
		if (!Bee_Utils_Strings::hasText($alias)) {
			$this->getReaderContext()->error('Alias must not be empty', $ele);
			$valid = false;
		}
		if ($valid) {
			try {
				$this->getReaderContext()->getRegistry()->registerAlias($name, $alias);
			} catch (Exception $ex) {
				$this->getReaderContext()->error("Failed to register alias '$alias' for bean with name '$name'", $ele, $ex);
			}
//			getReaderContext().fireAliasRegistered(name, alias, extractSource(ele));
		}
	}

	protected function importBeanDefinitionResource(DOMElement $ele) {
		throw new Exception('The beans:import tag is currently not supported by this parser!');
	}
	
	/**
	 * Process the given bean element, parsing the bean definition
	 * and registering it with the registry.
	 *
	 * @param DOMElement $ele
	 * @param Bee_Context_Xml_ParserDelegate $delegate
	 */
	protected function processBeanDefinition(DOMElement $ele, Bee_Context_Xml_ParserDelegate $delegate) {
		$bdHolder = $delegate->parseBeanDefinitionElement($ele);

		if (!is_null($bdHolder)) {
			$bdHolder = $delegate->decorateBeanDefinitionIfRequired($ele, $bdHolder);
			try {
				// Register the final decorated instance.
				Bee_Context_Support_BeanDefinitionReaderUtils::registerBeanDefinition($bdHolder, $this->getReaderContext()->getRegistry());
			} catch (Bee_Context_BeanDefinitionStoreException $ex) {
				$beanName = $bdHolder->getBeanName(); 
				$this->getReaderContext()->error("Failed to register bean definition with name '$beanName'", $ele, $ex);
			}
			// Send registration event.
//			getReaderContext().fireComponentRegistered(new BeanComponentDefinition(bdHolder));
		}
	}

	

	protected function preProcessXml(DOMElement $root) {
		// empty by default	
	}

	protected function postProcessXml(DOMElement $root) {
		// empty by default	
	}
}
?>