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
 * Method name resolver implementation based on Addendum Annotations. This is a very powerful implementation, capable of invoking different
 * handler methods based on both the path info of the request and the HTTP method used.
 * 
 * Handler methods in a delegate handler class must conform to the rules for handler methods for the multi action controller and be
 * annotated with <code>Bee_MVC_Controller_Multiaction_RequestHandler</code> annotations.
 * 
 * @see Bee_MVC_Controller_MultiAction
 * @see Bee_MVC_Controller_Multiaction_IMethodNameResolver
 * @see Bee_MVC_Controller_Multiaction_RequestHandler
 * 
 * @author Michael Plomer <michael.plomer@iter8.de> 
 */ 
class Bee_MVC_Controller_Multiaction_MethodNameResolver_AnnotationBased extends Bee_MVC_Controller_Multiaction_MethodNameResolver_Abstract {
	
	const DEFAULT_HTTP_METHOD_KEY = 'DEFAULT';

	private $methodResolvers = array();
	
	private $initialized = false;

	public function getHandlerMethodName(Bee_MVC_IHttpRequest $request) {
		
		$this->init();
				
		$httpMethod = strtoupper($request->getMethod());
		
		if(array_key_exists($httpMethod, $this->methodResolvers)) {
			$resolver = $this->methodResolvers[$httpMethod];
			$handlerMethod = $resolver->getHandlerMethodName($request);
		}
		
		if(is_null($handlerMethod)) {
			$resolver = $this->methodResolvers[self::DEFAULT_HTTP_METHOD_KEY];
			if(!is_null($resolver)) {
				$handlerMethod = $resolver->getHandlerMethodName($request);			
			}
		}
		
		return $handlerMethod;
	}

		
	protected function init() {
		if(!$this->initialized) {
			$delegate = $this->getController()->getDelegate();
			$classReflector = new ReflectionAnnotatedClass($delegate);

			$methods = $classReflector->getMethods(ReflectionMethod::IS_PUBLIC);
			
			$mappings = array();
			foreach($methods as $method) {
				
				if($this->getController()->isHandlerMethod($method)) {
					
					// is possible handler method, check for annotations
					$annotations = $method->getAllAnnotations('Bee_MVC_Controller_Multiaction_RequestHandler');

					foreach($annotations as $annotation) {
						$httpMethod = strtoupper($annotation->httpMethod);
						if(!Bee_Utils_Strings::hasText($httpMethod)) {
							$httpMethod = self::DEFAULT_HTTP_METHOD_KEY;
						}
						if(!array_key_exists($httpMethod, $mappings)) {
							$mappings[$httpMethod] = array();
						}
						
						// replace special escape syntax needed to allow */ patterns in PHPDoc comments
						$pathPattern = str_replace('*\/', '*/', $annotation->pathPattern);
						$mappings[$httpMethod][$pathPattern] = $method;
					}

				}

			}
			
			foreach($mappings as $method => $mapping) {
				$resolver = new Bee_MVC_Controller_Multiaction_MethodNameResolver_AntPath();
				$resolver->setMethodMappings($mapping);
				$this->methodResolvers[$method] = $resolver;
			}
		}
	}
}
?>