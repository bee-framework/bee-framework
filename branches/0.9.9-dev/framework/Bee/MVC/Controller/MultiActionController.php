<?php
namespace Bee\MVC\Controller;
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
use Bee\MVC\Controller\Multiaction\IHandlerMethodInvocator;
use Bee\MVC\Controller\Multiaction\IMethodNameResolver;
use Bee\MVC\Controller\Multiaction\NoHandlerMethodFoundException;
use Bee\MVC\IDelegatingHandler;
use Bee\MVC\IHttpRequest;
use Bee\MVC\IWidgetController;
use Bee\MVC\ModelAndView;
use Bee\Utils\Assert;
use Bee\Utils\Reflection;
use Bee\Utils\Strings;
use Bee\Utils\Types;
use Exception;
use ReflectionMethod;

/**
 * Class MultiActionController
 *
 * The multi-action controller handles requests by delegating to method calls on a delegate object. The methods to be
 * used are determined by the method name resolver.
 *
 * replaces Bee_MVC_Controller_MultiAction
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 * @author Benjamin Hartmann
 */
class MultiActionController extends AbstractController implements IDelegatingHandler, IWidgetController {

	/**
	 * Enter description here...
	 *
	 * @todo: What is the rationale behind having the defaultMethodName in the Controller rather than in the MethodNameResolver (Spring places it in the latter)?
	 * @var String
	 */
	private $defaultMethodName;

	/**
	 * Enter description here...
	 *
	 * @var object
	 */
	private $delegate;

	/**
	 * @var IHandlerMethodInvocator
	 */
	private $methodInvocator;

	/**
	 * Enter description here...
	 *
	 * @var IMethodNameResolver
	 */
	private $methodNameResolver;

	protected function init() {
		Assert::notNull($this->delegate, '\'delegate\' property is required in ' . __CLASS__);
		Assert::isTrue(
			!is_null($this->methodInvocator) || !is_null($this->methodNameResolver),
				'either \'methodInvocator\' or \'methodNameResolver\' property required in '  . __CLASS__
		);
	}

	/**
	 * Enter description here...
	 *
	 * @param IHttpRequest $request
	 * @throws Exception
	 * @return ModelAndView
	 */
	protected function handleRequestInternally(IHttpRequest $request) {
		if(!is_null($this->methodInvocator)) {
			return $this->methodInvocator->invokeHandlerMethod($request);
		}
		$methodName = $this->methodNameResolver->getHandlerMethodName($request);
		if ($methodName instanceof ReflectionMethod) {
			$method = $methodName;
		} else {
			if (!Strings::hasText($methodName)) {
				$methodName = $this->getDefaultMethodName();
			}
			$method = $this->getReflectionMethodForName($methodName);
		}
		return $method->invokeArgs($this->delegate, array($request));
	}

	/**
	 * @param ReflectionMethod $method
	 * @return bool
	 */
	public final function isHandlerMethod(ReflectionMethod $method) {
		if (Reflection::isCallableRegularMethod($method)) {
			$parameters = $method->getParameters();
			$paramCount = count($parameters);
			if ($paramCount < 1) {
				return false;
			}
			// first param must be of type Bee\MVC\IHttpRequest
			$class1 = $parameters[0]->getClass();
			if (is_null($class1) || $class1->getName() !== 'Bee\MVC\IHttpRequest') {
				return false;
			}
			// @todo: 2nd parameter can be required for exception handlers or if command object
			// all remaining params must be optional
			for ($i = 1; $i < $paramCount; $i++) {
				$param = $parameters[$i];
				if (!$param->isOptional()) {
					return false;
				}
			}
			return true;
		}
		return false;
	}

	/**
	 * Enter description here...
	 *
	 * @return String
	 */
	public function getDefaultMethodName() {
		return $this->defaultMethodName;
	}

	/**
	 * Enter description here...
	 *
	 * @param String $defaultMethodName
	 */
	public function setDefaultMethodName($defaultMethodName) {
		$this->defaultMethodName = $defaultMethodName;
	}

	/**
	 * Enter description here...
	 *
	 * @param object $delegate
	 */
	public final function setDelegate($delegate) {
		$this->delegate = $delegate;
	}

	/**
	 * Enter description here...
	 *
	 * @return object
	 */
	public function getDelegate() {
		return $this->delegate;
	}

	/**
	 * @param IHandlerMethodInvocator $methodInvocator
	 */
	public function setMethodInvocator(IHandlerMethodInvocator $methodInvocator) {
		$this->methodInvocator = $methodInvocator;
		$this->methodInvocator->setDelegatingHandler($this);
	}

	/**
	 * @return IHandlerMethodInvocator
	 */
	public function getMethodInvocator() {
		return $this->methodInvocator;
	}

	/**
	 * Enter description here...
	 *
	 * @param IMethodNameResolver $methodNameResolver
	 * @return void
	 */
	public final function setMethodNameResolver(IMethodNameResolver $methodNameResolver) {
		$this->methodNameResolver = $methodNameResolver;
		$this->methodNameResolver->setDelegatingHandler($this);
	}

	/**
	 * Enter description here...
	 *
	 * @return IMethodNameResolver
	 */
	public function getMethodNameResolver() {
		return $this->methodNameResolver;
	}

	/**
	 * @param IHttpRequest $request
	 * @return mixed
	 * @throws NoHandlerMethodFoundException
	 */
	public function handleDefault(IHttpRequest $request) {
		if(!is_null($this->methodInvocator)) {
			return $this->methodInvocator->invokeDefaultHandlerMethod($request);
		}
		return $this->getReflectionMethodForName($this->getDefaultMethodName())->invokeArgs($this->delegate, array($request));
	}

	/**
	 * @param string $methodName
	 * @return ReflectionMethod
	 * @throws NoHandlerMethodFoundException
	 */
	protected function getReflectionMethodForName($methodName) {
		// @todo: this might pose a security risk. introduce a set of allowed method names
		$method = new ReflectionMethod($this->delegate, $methodName);
		if (!$this->isHandlerMethod($method)) {
			throw new NoHandlerMethodFoundException('No request handling method with name ' . $methodName . ' in class [' . Types::getType($this->delegate) . ']');
		}
		return $method;
	}
} 