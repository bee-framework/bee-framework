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
use Addendum\ReflectionAnnotatedMethod;
use Bee\MVC\Controller\Multiaction\AbstractAnnotationBasedResolver;
use Bee\MVC\Controller\Multiaction\IHandlerMethodInvocator;
use Bee_MVC_IHttpRequest;

/**
 * Class AnnotationBasedInvocator
 * @package Bee\MVC\Controller\Multiaction\HandlerMethodInvocator
 */
class AnnotationBasedInvocator extends AbstractAnnotationBasedResolver implements IHandlerMethodInvocator  {

	/**
	 * @param Bee_MVC_IHttpRequest $request
	 * @return \Bee_MVC_ModelAndView
	 */
	public function invokeHandlerMethod(Bee_MVC_IHttpRequest $request) {
		$resolvedMethod = $this->resolveMethodForRequest($request);
	}

	/**
	 * @param $delegate
	 * @param Bee_MVC_IHttpRequest $request
	 * @return mixed
	 */
	protected function obtainMethodFromDelegate($delegate, Bee_MVC_IHttpRequest $request) {
		// TODO: Implement obtainMethodFromDelegate() method.
	}

	/**
	 * @param $pathPattern
	 * @param ReflectionAnnotatedMethod $method
	 * @return mixed
	 */
	protected function massagePathPattern($pathPattern, ReflectionAnnotatedMethod $method) {
		// TODO: Implement massagePathPattern() method.
	}

	/**
	 * @param array $mapping
	 * @return mixed
	 */
	protected function createDelegate(array $mapping) {
		// TODO: Implement createDelegate() method.
	}
}