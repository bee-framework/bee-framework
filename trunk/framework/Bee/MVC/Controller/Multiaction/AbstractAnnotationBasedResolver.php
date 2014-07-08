<?php
namespace Bee\MVC\Controller\Multiaction;

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
use Addendum\ReflectionAnnotatedClass;
use Addendum\ReflectionAnnotatedMethod;
use Bee_Cache_Manager;
use Bee_Framework;
use Bee_MVC_IHttpRequest;
use Bee_Utils_Reflection;
use Bee_Utils_Strings;
use Exception;
use ReflectionMethod;

/**
 * Class AbstractAnnotationBasedResolver
 * @package Bee\MVC\Controller\Multiaction
 */
abstract class AbstractAnnotationBasedResolver extends AbstractControllerHolder {

	const DEFAULT_HTTP_METHOD_KEY = 'DEFAULT';
	const AJAX_TYPE_TRUE_KEY = '_TRUE';
	const AJAX_TYPE_FALSE_KEY = '_FALSE';
	const AJAX_TYPE_ANY_KEY = '_ANY';

	const CACHE_KEY_PREFIX = 'BeeMethodResolverAnnotationCache_';

	/**
	 * @var
	 */
	private $delegates;

	/**
	 * @param Bee_MVC_IHttpRequest $request
	 * @return mixed
	 */
	protected function resolveMethodForRequest(Bee_MVC_IHttpRequest $request) {
		$this->init();
		$httpMethod = $this->getMethodNameKey($request->getMethod());
		$ajaxKeyPart = $this->getAjaxTypeKey($request->getAjax());

		$possibleMethodKeys = array(
				$httpMethod . $ajaxKeyPart,
				$httpMethod . self::AJAX_TYPE_ANY_KEY,
				self::DEFAULT_HTTP_METHOD_KEY . $ajaxKeyPart,
				self::DEFAULT_HTTP_METHOD_KEY . self::AJAX_TYPE_ANY_KEY
		);

		$resolvedMethod = null;
		foreach ($possibleMethodKeys as $methodKey) {
			if (array_key_exists($methodKey, $this->delegates)) {
				$resolvedMethod = $this->obtainMethodFromDelegate($this->delegates[$methodKey], $request);
				if (!is_null($resolvedMethod)) {
					break;
				}
			}
		}
		return $resolvedMethod;
	}

	/**
	 * @param $delegate
	 * @param Bee_MVC_IHttpRequest $request
	 * @return mixed
	 */
	abstract protected function obtainMethodFromDelegate($delegate, Bee_MVC_IHttpRequest $request);

	/**
	 * @param $pathPattern
	 * @param ReflectionAnnotatedMethod $method
	 * @return mixed
	 */
	protected function massagePathPattern($pathPattern, ReflectionAnnotatedMethod $method) {
		// replace special escape syntax needed to allow */ patterns in PHPDoc comments
		return str_replace('*\/', '*/', $pathPattern);
	}

	/**
	 * @param array $mapping
	 * @return mixed
	 */
	abstract protected function createDelegate(array $mapping);

	/**
	 *
	 */
	protected function init() {
		if (!$this->delegates) {

			$delegateClassName = get_class($this->getController()->getDelegate());
			if (Bee_Framework::getProductionMode()) {
				try {
					$this->delegates = Bee_Cache_Manager::retrieve(self::CACHE_KEY_PREFIX . $delegateClassName);
				} catch (Exception $e) {
					$this->getLog()->debug('No cached delegate resolvers for delegate "' . $delegateClassName . '" found, annotation parsing required');
				}
			}

			if (!$this->delegates) {
				$classReflector = new ReflectionAnnotatedClass($delegateClassName);

				/** @var ReflectionAnnotatedMethod[] $methods */
				$methods = $classReflector->getMethods(ReflectionMethod::IS_PUBLIC);

				$mappings = array();
				foreach ($methods as $method) {

					if (Bee_Utils_Reflection::isCallableRegularMethod($method)) {

						// is possible handler method, check for annotations
						/** @var \Bee_MVC_Controller_Multiaction_RequestHandler[] $annotations */
						$annotations = $method->getAllAnnotations('Bee_MVC_Controller_Multiaction_RequestHandler');
						foreach ($annotations as $annotation) {
							$requestTypeKey = $this->getMethodNameKey($annotation->httpMethod) .
									$this->getAjaxTypeKey(filter_var($annotation->ajax, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE));
							if (!array_key_exists($requestTypeKey, $mappings)) {
								$mappings[$requestTypeKey] = array();
							}

							$pathPattern = $this->massagePathPattern($annotation->pathPattern, $method);
							$mappings[$requestTypeKey][$pathPattern] = $method;
						}
					}
				}

				foreach ($mappings as $requestTypeKey => $mapping) {
					$this->delegates[$requestTypeKey] = $this->createDelegate($mapping);
				}
			}

			if (Bee_Framework::getProductionMode()) {
				Bee_Cache_Manager::store(self::CACHE_KEY_PREFIX . $delegateClassName, $this->delegates);
			}
		}
	}

	/**
	 * @param $methodName
	 * @return string
	 */
	protected function getMethodNameKey($methodName) {
		return Bee_Utils_Strings::hasText($methodName) ? strtoupper($methodName) : self::DEFAULT_HTTP_METHOD_KEY;
	}

	/**
	 * @param bool|null $ajax
	 * @return string
	 */
	protected function getAjaxTypeKey($ajax) {
		if (is_null($ajax)) {
			return self::AJAX_TYPE_ANY_KEY;
		}
		return $ajax ? self::AJAX_TYPE_TRUE_KEY : self::AJAX_TYPE_FALSE_KEY;
	}
}