<?php
namespace Bee\MVC\Controller\Multiaction\HandlerMethodInvocator;
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
use Bee\Beans\PropertyEditor\PropertyEditorRegistry;
use Bee\Context\Config\IContextAware;
use Bee\IContext;
use Bee\MVC\Controller\Multiaction\AbstractAnnotationBasedResolver;
use Bee\MVC\Controller\Multiaction\IHandlerMethodInvocator;
use Bee\MVC\IHttpRequest;
use Bee\MVC\ModelAndView;

/**
 * Class AnnotationBasedInvocator
 * @package Bee\MVC\Controller\Multiaction\HandlerMethodInvocator
 */
class AnnotationBasedInvocator extends AbstractAnnotationBasedResolver implements IHandlerMethodInvocator, IContextAware {

	const DEFAULT_METHOD_CACHE_KEY_PREFIX = 'BeeDefaultHandlerMethodCache_';

	/**
	 * @var string
	 */
	private $defaultMethodName;

	/**
	 * @var PropertyEditorRegistry
	 */
	private $propertyEditorRegistry;

	/**
	 * @var MethodInvocation
	 */
	private $defaultMethodInvocation;

	/**
	 * @return array
	 */
	protected function getDefaultMethodInvocation() {
		if (!$this->defaultMethodInvocation instanceof MethodInvocation) {
			// todo: fetch default invocation from cache or rebuild

			$ctrl = $this->getDelegatingHandler();
			$delegateClassName = get_class($ctrl->getDelegate());
			$defaultMethodName = $this->getDefaultMethodName() ?: $ctrl->getDefaultMethodName();

//			$cacheKey = self::DEFAULT_METHOD_CACHE_KEY_PREFIX . $delegateClassName.'::'.$defaultMethodName;
//			if (Framework::getProductionMode()) {
//				try {
//					$this->defaultMethodInvocation = Manager::retrieve($cacheKey);
//				} catch (Exception $e) {
//					$this->getLog()->debug('No cached default method invocation for "' . $delegateClassName . '::'. $defaultMethodName .'" found, annotation parsing required');
//				}
//			}

			if (!$this->defaultMethodInvocation) {
				$this->defaultMethodInvocation = new MethodInvocation(RegexMappingInvocationResolver::getCachedMethodMetadata(new \ReflectionMethod($delegateClassName, $defaultMethodName), $this->getPropertyEditorRegistry()));
			}

			// todo: fix production mode method caching
//			if (Framework::getProductionMode()) {
//				Manager::store($cacheKey, $this->defaultMethodInvocation);
//			}
		}
		return $this->defaultMethodInvocation;
	}

	/**
	 * @param IHttpRequest $request
	 * @param array $fixedParams
	 * @return ModelAndView
	 */
	public function invokeHandlerMethod(IHttpRequest $request, array $fixedParams = array()) {
		/** @var MethodInvocation $resolvedMethod */
		$resolvedMethod = $this->resolveMethodForRequest($request);
		if (is_null($resolvedMethod)) {
			$resolvedMethod = $this->getDefaultMethodInvocation();
		}

		// todo:
		return $this->invokeResolvedMethod($resolvedMethod, $request, $fixedParams);
	}

	/**
	 * @param MethodInvocation $resolvedMethod
	 * @param IHttpRequest $request
	 * @param array $fixedParams
	 * @return mixed
	 */
	protected function invokeResolvedMethod(MethodInvocation $resolvedMethod, IHttpRequest $request, array $fixedParams = array()) {
		$methodMeta = $resolvedMethod->getMethodMeta();
		$method = $methodMeta->getMethod();
		$args = array();
		foreach ($method->getParameters() as $parameter) {
			$pos = $parameter->getPosition();
			$type = $methodMeta->getTypeMapping($pos);
			if ($type == 'Bee\MVC\IHttpRequest') {
				$args[$pos] = $request;
			} else if (array_key_exists($type, $fixedParams)) {
				$args[$pos] = $fixedParams[$type];
			} else {
				$propEditor = $this->propertyEditorRegistry->getEditor($type);
				$posMap = $resolvedMethod->getUrlParameterPositions();
				$value = array_key_exists($pos, $posMap) ? $resolvedMethod->getParamValue($posMap[$pos]) :
					(array_key_exists($parameter->getName(), $_REQUEST) ? $_REQUEST[$parameter->getName()] : null);
				if (!is_null($value) || !$parameter->isOptional()) {
					$args[$pos] = $propEditor->fromString($value);
				} else {
					$args[$pos] = null;
				}
			}
		}
		return $method->invokeArgs($this->getDelegatingHandler()->getDelegate(), $args);
	}

	/**
	 * @param IInvocationResolver $delegate
	 * @param IHttpRequest $request
	 * @return mixed
	 */
	protected function obtainMethodFromDelegate($delegate, IHttpRequest $request) {
		return $delegate->getInvocationDefinition($request);
	}

	/**
	 * @param array $mapping
	 * @return mixed
	 */
	protected function createDelegate(array $mapping) {
		return new RegexMappingInvocationResolver($mapping, $this->getPropertyEditorRegistry());
	}

	/**
	 * @param IContext $context
	 */
	public function setBeeContext(IContext $context) {
		$this->propertyEditorRegistry = new PropertyEditorRegistry($context);
	}

	/**
	 * @return PropertyEditorRegistry
	 */
	public function getPropertyEditorRegistry() {
		return $this->propertyEditorRegistry;
	}

	/**
	 * @return string
	 */
	public function getDefaultMethodName() {
		return $this->defaultMethodName;
	}

	/**
	 * @param string $defaultMethodName
	 */
	public function setDefaultMethodName($defaultMethodName) {
		$this->defaultMethodName = $defaultMethodName;
	}

	/**
	 * @param IHttpRequest $request
	 * @return mixed
	 */
	public function invokeDefaultHandlerMethod(IHttpRequest $request) {
		return $this->invokeResolvedMethod($this->getDefaultMethodInvocation(), $request);
	}
}