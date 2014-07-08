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
use Bee_Context_Config_IContextAware;
use Bee_IContext;
use Bee_MVC_IHttpRequest;

/**
 * Class AnnotationBasedInvocator
 * @package Bee\MVC\Controller\Multiaction\HandlerMethodInvocator
 */
class AnnotationBasedInvocator extends AbstractAnnotationBasedResolver implements IHandlerMethodInvocator, Bee_Context_Config_IContextAware {

	/**
	 * @var PropertyEditorRegistry
	 */
	private $propertyEditorRegistry;

	/**
	 * @param Bee_MVC_IHttpRequest $request
	 * @return \Bee_MVC_ModelAndView
	 */
	public function invokeHandlerMethod(Bee_MVC_IHttpRequest $request) {
		$resolvedMethod = $this->resolveMethodForRequest($request);

		/** @var \ReflectionMethod $method */
		$method = $resolvedMethod['method'];

		$args = array();

		foreach($method->getParameters() as $parameter) {
			$pos = $parameter->getPosition();
			$type = $resolvedMethod['typeMap'][$pos];
			if($type == 'Bee_MVC_IHttpRequest') {
				$args[$pos] = $request;
			} else {
				$propEditor = $this->propertyEditorRegistry->getEditor($type);
				$urlPos = $resolvedMethod['positionMap'][$pos];
				$args[$pos] = $propEditor->fromString($resolvedMethod['paramValues'][$urlPos]);
			}
		}
		$method->invokeArgs($this->getController()->getDelegate(), $args);
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
		return new RegexMappingInvocationResolver($mapping);
	}

	/**
	 * @param Bee_IContext $context
	 */
	public function setBeeContext(Bee_IContext $context) {
		$this->propertyEditorRegistry = new PropertyEditorRegistry($context);
	}
}