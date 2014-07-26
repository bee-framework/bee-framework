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
use Bee\Context\Support\BeanUtils;

/**
 * Enter description here...
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class Bee_Context_Xml_HardcodedNamespaceHandlerResolver implements Bee_Context_Xml_Namespace_IHandlerResolver {
	
	private static $NAMESPACE_HANDLERS = array(
		'http://www.beeframework.org/schema/aop' => 'Bee_AOP_Namespace_Handler',
		'http://www.beeframework.org/schema/security' => 'Bee_Security_Namespace_Handler',
		'http://www.beeframework.org/schema/tx' => null,
		'http://www.beeframework.org/schema/util' => 'Bee_Context_Util_Namespace_Handler',
		'http://www.beeframework.org/schema/batch' => 'Bee\Tools\Batch\XmlNamespace\Handler',
		'http://www.beeframework.org/schema/mvc' => 'Bee\MVC\XmlNamespace\Handler'
	);
	
	public function resolve($namespaceUri) {
		$handlerOrClassName = self::$NAMESPACE_HANDLERS[$namespaceUri];
		if(is_null($handlerOrClassName) || $handlerOrClassName instanceof Bee_Context_Xml_Namespace_IHandler) {
			return $handlerOrClassName;
		} else {
			$handler = BeanUtils::instantiateClass($handlerOrClassName);
			if($handler instanceof Bee_Context_Xml_Namespace_IHandler) {
				$handler->init();
				self::$NAMESPACE_HANDLERS[$namespaceUri] = $handler;
				return $handler;
			} else {
				throw new Exception("Namespace handler configured for namespace $namespaceUri in not an implementation of Bee_Context_Namespace_IHandler");
			}
		}
		throw new Exception("Could not resolve namespace handler for namespace $namespaceUri.");
	}
}