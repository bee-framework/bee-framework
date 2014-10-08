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
use Bee\Context\Support\BeanUtils;
use Bee\Context\Xml\XmlNamespace\IHandler;
use Bee\Context\Xml\XmlNamespace\IHandlerResolver;
use Exception;

/**
 * Enter description here...
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class DefaultNamespaceHandlerResolver implements IHandlerResolver {
	
	private static $NAMESPACE_HANDLERS = array(
		'http://www.beeframework.org/schema/aop' => 'Bee_AOP_Namespace_Handler',
		'http://www.beeframework.org/schema/security' => 'Bee_Security_Namespace_Handler',
		'http://www.beeframework.org/schema/tx' => null,
		'http://www.beeframework.org/schema/util' => 'Bee\Context\Util\XmlNamespace\Handler',
		'http://www.beeframework.org/schema/mvc' => 'Bee\MVC\XmlNamespace\Handler'
	);

	/**
	 * @var bool
	 */
	private $initialized = false;

	/**
	 * @param String $namespaceUri
	 * @return IHandler|void
	 * @throws Exception
	 */
	public function resolve($namespaceUri) {
		$this->initialize();
		$handlerOrClassName = self::$NAMESPACE_HANDLERS[$namespaceUri];
		if(is_null($handlerOrClassName) || $handlerOrClassName instanceof IHandler) {
			return $handlerOrClassName;
		} else {
			$handler = BeanUtils::instantiateClass($handlerOrClassName);
			if($handler instanceof IHandler) {
				$handler->init();
				self::$NAMESPACE_HANDLERS[$namespaceUri] = $handler;
				return $handler;
			} else {
				throw new Exception("Namespace handler configured for namespace $namespaceUri in not an implementation of Bee\\Context\\Xml\\XmlNamespace\\IHandler");
			}
		}
	}

	/**
	 *
	 */
	protected function initialize() {
		if(!$this->initialized) {
			if(file_exists('namespaces.json')) {
				$nsConf = json_decode(file_get_contents('namespaces.json'), true);
				foreach($nsConf as $namespaceUri => $handlerClassName) {
					self::registerHandler($namespaceUri, $handlerClassName);
				}
			}
			$this->initialized = true;
		}
	}

	/**
	 * @param $namespaceUri
	 * @param $handlerClassName
	 */
	public static function registerHandler($namespaceUri, $handlerClassName) {
		// todo: check class existence or not? fail-fast vs. performance trade-off
		self::$NAMESPACE_HANDLERS[$namespaceUri] = $handlerClassName;
	}
}