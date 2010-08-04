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
class Bee_Context_Xml_ReaderContext {
	
	/**
	 * Enter description here...
	 *
	 * @var Bee_Context_Config_IBeanDefinitionRegistry
	 */
	private $registry;

	
	
	/**
	 * Enter description here...
	 *
	 * @var Bee_Context_Xml_Namespace_IHandlerResolver
	 */
	private $namespaceHandlerResolver;
	
	
	
	public function __construct(
			Bee_Context_Config_IBeanDefinitionRegistry $registry, 
			Bee_Context_Xml_Namespace_IHandlerResolver $namespaceHandlerResolver) {
		
		$this->registry = $registry;
		$this->namespaceHandlerResolver = $namespaceHandlerResolver;
	}
	
	
	
	/**
	 * Enter description here...
	 *
	 * @return Bee_Context_Config_IBeanDefinitionRegistry
	 */
	public function getRegistry() {
		return $this->registry;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return Bee_Context_Xml_Namespace_IHandlerResolver
	 */
	public function getNamespaceHandlerResolver() {
		return $this->namespaceHandlerResolver;
	}
	
	public function error($message, DOMElement $elem, Exception $ex = null) {
		// @todo: throw exception
		trigger_error($message, E_USER_ERROR);
	}

	public function warning($message, DOMElement $elem, Exception $ex = null) {
		// @todo
		trigger_error($message, E_USER_WARNING);
	}
	
	public function notice($message, DOMElement $elem, Exception $ex = null) {
		// @todo
		trigger_error($message, E_USER_NOTICE);
	}
	
}
?>