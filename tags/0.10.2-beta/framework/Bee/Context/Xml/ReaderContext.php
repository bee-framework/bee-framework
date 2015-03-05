<?php
namespace Bee\Context\Xml;
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
use Bee\Context\BeanCreationException;
use Bee\Context\Config\IBeanDefinitionRegistry;
use Bee\Context\Xml\XmlNamespace\IHandlerResolver;
use Bee\Framework;
use DOMElement;
use Exception;
use Logger;

/**
 * Enter description here...
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class ReaderContext {
	
	/**
	 * Enter description here...
	 *
	 * @var IBeanDefinitionRegistry
	 */
	private $registry;

	/**
	 * Enter description here...
	 *
	 * @var IHandlerResolver
	 */
	private $namespaceHandlerResolver;

    /**
     * @var Logger
     */
	private $log;

	/**
	 * @param IBeanDefinitionRegistry $registry
	 * @param IHandlerResolver $namespaceHandlerResolver
	 */
	public function __construct(
			IBeanDefinitionRegistry $registry,
			IHandlerResolver $namespaceHandlerResolver) {
		
        $this->log = Framework::getLoggerForClass(__CLASS__);

		$this->registry = $registry;
		$this->namespaceHandlerResolver = $namespaceHandlerResolver;
	}

	/**
	 * Enter description here...
	 *
	 * @return IBeanDefinitionRegistry
	 */
	public function getRegistry() {
		return $this->registry;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return IHandlerResolver
	 */
	public function getNamespaceHandlerResolver() {
		return $this->namespaceHandlerResolver;
	}

	/**
	 * @param $message
	 * @param DOMElement $elem
	 * @param Exception $ex
	 * @throws BeanCreationException
	 */
	public function error($message, DOMElement $elem, Exception $ex = null) {
//		trigger_error($message, E_USER_ERROR);
        $this->log->fatal($message);
        throw new BeanCreationException('', $message, $ex);
	}

	/**
	 * @param $message
	 * @param DOMElement $elem
	 * @param Exception $ex
	 */
	public function warning($message, DOMElement $elem, Exception $ex = null) {
        $this->log->warn($message);
	}

	/**
	 * @param $message
	 * @param DOMElement $elem
	 * @param Exception $ex
	 */
	public function notice($message, DOMElement $elem, Exception $ex = null) {
        $this->log->info($message);
	}
}
