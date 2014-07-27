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
use Bee\MVC\Controller\Multiaction\AbstractAnnotationBasedResolver;
use Bee\MVC\Controller\Multiaction\IHandlerMethodInvocator;
use Bee_Cache_Manager;
use Bee_Context_Config_IContextAware;
use Bee_Framework;
use Bee_IContext;
use Bee_MVC_IHttpRequest;
use Exception;

/**
 * Class AnnotationBasedInvocator
 * @package Bee\MVC\Controller\Multiaction\HandlerMethodInvocator
 */
class AnnotationBasedInvocator extends AbstractAnnotationBasedResolver implements IHandlerMethodInvocator, Bee_Context_Config_IContextAware {

	const DEFAULT_METHOD_CACHE_KEY_PREFIX = 'BeeDefaultHandlerMethodCache_';

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
		if (!is_array($this->defaultMethodInvocation)) {
			// todo: fetch default invocation from cache or rebuild

			$ctrl = $this->getController();
			$delegateClassName = get_class($ctrl->getDelegate());

			$cacheKey = self::DEFAULT_METHOD_CACHE_KEY_PREFIX . $delegateClassName.'::'.$ctrl->getDefaultMethodName();
			if (Bee_Framework::getProductionMode()) {
				try {
					$this->defaultMethodInvocation = Bee_Cache_Manager::retrieve($cacheKey);
				} catch (Exception $e) {
					$this->getLog()->debug('No cached default method invocation for "' . $delegateClassName . '::'. $ctrl->getDefaultMethodName() .'" found, annotation parsing required');
				}
			}

			if (!$this->defaultMethodInvocation) {
				$methodMeta = RegexMappingInvocationResolver::getCachedMethodMetadata(new \ReflectionMethod($delegateClassName, $ctrl->getDefaultMethodName()), $this->getPropertyEditorRegistry());
				$this->defaultMethodInvocation = new MethodInvocation($methodMeta);
			}

			if (Bee_Framework::getProductionMode()) {
				Bee_Cache_Manager::store($cacheKey, $this->defaultMethodInvocation);
			}
		}
		return $this->defaultMethodInvocation;
	}

	/**
	 * @param Bee_MVC_IHttpRequest $request
	 * @return \Bee_MVC_ModelAndView
	 */
	public function invokeHandlerMethod(Bee_MVC_IHttpRequest $request) {
		/** @var MethodInvocation $resolvedMethod */
		$resolvedMethod = $this->resolveMethodForRequest($request);

		if (is_null($resolvedMethod)) {
			$resolvedMethod = $this->getDefaultMethodInvocation();
		}

		$methodMeta = $resolvedMethod->getMethodMeta();
		$method = $methodMeta->getMethod();
		$args = array();
		foreach ($method->getParameters() as $parameter) {
			$pos = $parameter->getPosition();
			$type = $methodMeta->getTypeMapping($pos);
			if ($type == 'Bee_MVC_IHttpRequest') {
				$args[$pos] = $request;
			} else {
				$propEditor = $this->propertyEditorRegistry->getEditor($type);
				$posMap = $resolvedMethod->getUrlParameterPositions();
				$value = array_key_exists($pos, $posMap) ? $resolvedMethod->getParamValue($posMap[$pos]) :
						(array_key_exists($parameter->getName(), $_REQUEST) ? $_REQUEST[$parameter->getName()] : null);
				if (!is_null($value) || !$parameter->isOptional()) {
					$args[$pos] = $propEditor->fromString($value);
				}
			}
		}
		return $method->invokeArgs($this->getController()->getDelegate(), $args);
	}

	/**
	 * @param IInvocationResolver $delegate
	 * @param Bee_MVC_IHttpRequest $request
	 * @return mixed
	 */
	protected function obtainMethodFromDelegate($delegate, Bee_MVC_IHttpRequest $request) {
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
	 * @param Bee_IContext $context
	 */
	public function setBeeContext(Bee_IContext $context) {
		$this->propertyEditorRegistry = new PropertyEditorRegistry($context);
	}

	/**
	 * @return PropertyEditorRegistry
	 */
	public function getPropertyEditorRegistry() {
		return $this->propertyEditorRegistry;
	}
}