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
use Addendum\ReflectionAnnotatedClass;

/**
 * Method name resolver implementation based on Addendum Annotations. This is a very powerful implementation, capable of invoking different
 * handler methods based on both the path info of the request and the HTTP method used.
 * 
 * Handler methods in a delegate handler class must conform to the rules for handler methods for the multi action controller and be
 * annotated with <code>Bee_MVC_Controller_Multiaction_RequestHandler</code> annotations.
 *
 * todo: replace addendum annotation handling with Doctrine Annotations
 * 
 * @see Bee_MVC_Controller_MultiAction
 * @see Bee_MVC_Controller_Multiaction_IMethodNameResolver
 * @see Bee_MVC_Controller_Multiaction_RequestHandler
 * 
 * @author Michael Plomer <michael.plomer@iter8.de> 
 */ 
class Bee_MVC_Controller_Multiaction_MethodNameResolver_AnnotationBased extends Bee_MVC_Controller_Multiaction_MethodNameResolver_Abstract {
	
	const DEFAULT_HTTP_METHOD_KEY = 'DEFAULT';
	const AJAX_TYPE_TRUE_KEY = '_TRUE';
	const AJAX_TYPE_FALSE_KEY = '_FALSE';
	const AJAX_TYPE_ANY_KEY = '_ANY';

	const CACHE_KEY_PREFIX = 'BeeMethodNameResolverAnnotationCache_';

	/**
	 * @var Logger
	 */
	protected $log;

	/**
	 * @return Logger
	 */
	protected function getLog() {
		if (!$this->log) {
			$this->log = Logger::getLogger(get_class($this));
		}
		return $this->log;
	}

    /**
     * @var Bee_MVC_Controller_Multiaction_MethodNameResolver_AntPath[]
     */
	private $methodResolvers = false;

	/**
	 * @param Bee_MVC_IHttpRequest $request
	 * @return string
	 */
	public function getHandlerMethodName(Bee_MVC_IHttpRequest $request) {
		
		$this->init();
				
		$httpMethod = strtoupper($request->getMethod());
		
		$ajaxKeyPart = $this->getAjaxTypeKey($request->getAjax());
		return $this->selectHandlerMethod(array(
			$httpMethod . $ajaxKeyPart,
			$httpMethod . self::AJAX_TYPE_ANY_KEY,
			self::DEFAULT_HTTP_METHOD_KEY . $ajaxKeyPart,
			self::DEFAULT_HTTP_METHOD_KEY . self::AJAX_TYPE_ANY_KEY
		), $request);
	}

	/**
	 * @param array $possibleMethodKeys
	 * @param Bee_MVC_IHttpRequest $request
	 * @return string
	 */
	private function selectHandlerMethod(array $possibleMethodKeys, Bee_MVC_IHttpRequest $request) {
		$result = null;
		foreach($possibleMethodKeys as $methodKey) {
			if(array_key_exists($methodKey, $this->methodResolvers)) {
				$result = $this->methodResolvers[$methodKey]->getHandlerMethodName($request);
				if(!is_null($result)) {
					return $result;
				}
			}
		}
		return $result;
	}
		
	protected function init() {
		if(!$this->methodResolvers) {

			$delegateClassName = get_class($this->getController()->getDelegate());

			if(Bee_Framework::getProductionMode()) {
                try {
                    $this->methodResolvers = Bee_Cache_Manager::retrieve(self::CACHE_KEY_PREFIX . $delegateClassName);
                } catch (Exception $e) {
					$this->getLog()->debug('No cached method name resolvers for delegate "' . $delegateClassName . '" found, annotation parsing required');
                }
			}

			if(!$this->methodResolvers) {
				$classReflector = new ReflectionAnnotatedClass($delegateClassName);

				/** @var \Addendum\ReflectionAnnotatedMethod[] $methods */
				$methods = $classReflector->getMethods(ReflectionMethod::IS_PUBLIC);

				$mappings = array();
				foreach($methods as $method) {

					if($this->getController()->isHandlerMethod($method)) {

						// is possible handler method, check for annotations
						/** @var Bee_MVC_Controller_Multiaction_RequestHandler[] $annotations */
						$annotations = $method->getAllAnnotations('Bee_MVC_Controller_Multiaction_RequestHandler');

						foreach($annotations as $annotation) {
							$httpMethod = strtoupper($annotation->httpMethod);
							$ajax = filter_var($annotation->ajax, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
							$ajax = $this->getAjaxTypeKey($ajax);
							if(!Bee_Utils_Strings::hasText($httpMethod)) {
								$httpMethod = self::DEFAULT_HTTP_METHOD_KEY;
							}
							$httpMethod .= $httpMethod . $ajax;
							if(!array_key_exists($httpMethod, $mappings)) {
								$mappings[$httpMethod] = array();
							}

							// replace special escape syntax needed to allow */ patterns in PHPDoc comments
							$pathPattern = str_replace('*\/', '*/', $annotation->pathPattern);
							$mappings[$httpMethod][$pathPattern] = $method->getName();
						}
					}
				}

				foreach($mappings as $method => $mapping) {
					$resolver = new Bee_MVC_Controller_Multiaction_MethodNameResolver_AntPath();
					$resolver->setMethodMappings($mapping);
					$this->methodResolvers[$method] = $resolver;
				}
			}

			if(Bee_Framework::getProductionMode()) {
				Bee_Cache_Manager::store(self::CACHE_KEY_PREFIX.$delegateClassName, $this->methodResolvers);
			}
		}
	}

	/**
	 * @param bool|null $ajax
	 * @return string
	 */
	protected function getAjaxTypeKey($ajax) {
		if(is_null($ajax)) {
			return self::AJAX_TYPE_ANY_KEY;
		}
		return $ajax ? self::AJAX_TYPE_TRUE_KEY : self::AJAX_TYPE_FALSE_KEY;
	}
}