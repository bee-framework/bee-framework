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
use Bee\Context\Config\IBeanDefinitionRegistry;

/**
 * Enter description here...
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class Bee_Context_Xml_BeanDefinitionReader {

	/**
	 * Enter description here...
	 *
	 * @var IBeanDefinitionRegistry
	 */
	private $registry;
	
	/**
	 * Enter description here...
	 *
	 * @var Bee_Context_Xml_Namespace_IHandlerResolver
	 */
	private $namespaceHandlerResolver;

	/**
	 * Enter description here...
	 *
	 * @var array
	 */
	private $resourcesBeingLoaded = array(); 

	public function __construct(IBeanDefinitionRegistry $registry) {
		Bee_Utils_Assert::notNull($registry, 'Bean definition registry must not be NULL');
		$this->registry = $registry;
	}

	/**
	 * Enter description here...
	 *
	 * @param String $location
	 * @throws Bee_Context_BeansException
	 * @throws Exception
	 */
	public function loadBeanDefinitions($location) {
		Bee_Utils_Assert::hasText($location, 'You must specify a location from which to load bean definitions!');
		
		if(array_key_exists($location, $this->resourcesBeingLoaded)) {
			throw new Bee_Context_BeansException('Detected recursive loading of ' . $location . ' - check your import definitions!');
		}
		$this->resourcesBeingLoaded[$location] = true;
		try {
			$document = new DOMDocument();
			$document->load($location);
            if (is_null($document->documentElement)) {
                throw new Exception('Failed to load XML document: '.$location);
            }
			$this->registerBeanDefinitions($document);
		} catch (Exception $ex) {
			unset($this->resourcesBeingLoaded[$location]);
			throw $ex;
		}
	}

	/**
	 * @param DOMDocument $doc
	 * @return int
	 */
	public function registerBeanDefinitions(DOMDocument $doc) {
		$documentReader = new Bee_Context_Xml_BeanDefinitionDocumentReader();
		$countBefore = $this->getRegistry()->getBeanDefinitionCount();
		$documentReader->registerBeanDefinitions($doc, $this->createReaderContext());
		return $this->getRegistry()->getBeanDefinitionCount() - $countBefore;
	}

	/**
	 * Enter description here...
	 *
	 * @return Bee_Context_Xml_ReaderContext
	 */
	protected function createReaderContext() {
		if(is_null($this->namespaceHandlerResolver)) {
			$this->namespaceHandlerResolver = $this->createDefaultNamespaceHandlerResolver();
		}
		return new Bee_Context_Xml_ReaderContext($this->getRegistry(), $this->namespaceHandlerResolver);
	}

	/**
	 * Enter description here...
	 *
	 * @return Bee_Context_Xml_Namespace_IHandlerResolver
	 */
	protected function createDefaultNamespaceHandlerResolver() {
		return new Bee_Context_Xml_HardcodedNamespaceHandlerResolver();
	}

	/**
	 * Enter description here...
	 *
	 * @return IBeanDefinitionRegistry
	 */
	public function getRegistry() {
		return $this->registry;
	}
}