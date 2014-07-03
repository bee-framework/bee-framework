<?php
namespace Bee\MVC\Controller\Multiaction\MethodNameResolver;
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
use Bee\MVC\Controller\Multiaction\IMethodNameResolver;
use Bee_MVC_IHttpRequest;
use ReflectionMethod;

/**
 * Method name resolver implementation based on Addendum Annotations. This is a very powerful implementation, capable of invoking different
 * handler methods based on both the path info of the request and the HTTP method used.
 *
 * Handler methods in a delegate handler class must conform to the rules for handler methods for the multi action controller and be
 * annotated with <code>Bee_MVC_Controller_Multiaction_RequestHandler</code> annotations.
 *
 * todo: replace addendum annotation handling with Doctrine Annotations
 *
 * @see Bee\MVC\Controller\MultiAction
 * @see Bee\MVC\Controller\Multiaction\IMethodNameResolver
 * @see Bee_MVC_Controller_Multiaction_RequestHandler
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class AnnotationBasedMethodNameResolver extends AbstractAnnotationBasedResolver implements IMethodNameResolver {

	/**
	 * @param Bee_MVC_IHttpRequest $request
	 * @return string
	 */
	public function getHandlerMethodName(Bee_MVC_IHttpRequest $request) {
		return $this->resolveMethodForRequest($request);
	}

	/**
	 * @param AntPathMethodNameResolver $delegate
	 * @param Bee_MVC_IHttpRequest $request
	 * @return mixed
	 */
	protected function obtainMethodFromDelegate($delegate, Bee_MVC_IHttpRequest $request) {
		return $delegate->getHandlerMethodName($request);
	}

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
	protected function createDelegate(array $mapping) {
		$resolver = new AntPathMethodNameResolver();
		$resolver->setMethodMappings(array_map(function (ReflectionMethod &$method) {
			return $method->getName();
		}, $mapping));
		return $resolver;
	}
}