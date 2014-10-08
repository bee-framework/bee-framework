<?php
namespace Bee\MVC\Interceptor;
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
use Bee\Context\Config\IContextAware;
use Bee\IContext;
use Bee\MVC\Controller\Multiaction\HandlerMethodInvocator\AnnotationBasedInvocator;
use Bee\MVC\Controller\Multiaction\IHandlerMethodInvocator;
use Bee\MVC\IController;
use Bee\MVC\IDelegatingHandler;
use Bee\MVC\IHandlerInterceptor;
use Bee\MVC\IHttpRequest;
use Bee\MVC\ModelAndView;
use Exception;

/**
 * Class PathDelegatingInterceptor
 * An interceptor implementation that delegates method calls based on path patterns, akin to a MultiAction controller
 * @package Bee\MVC\Interceptor
 */
class PathDelegatingInterceptor implements IHandlerInterceptor, IDelegatingHandler, IContextAware {

	/**
	 * Enter description here...
	 *
	 * @var object
	 */
	private $delegate;

	/**
	 * @var IHandlerMethodInvocator
	 */
	private $preHandleMethodInvocator;

	/**
	 * @var IHandlerMethodInvocator
	 */
	private $postHandleMethodInvocator;

	/**
	 * @var IHandlerMethodInvocator
	 */
	private $afterCompletionMethodInvocator;

	/**
	 * @var IContext
	 */
	private $context;

	/**
	 * Enter description here...
	 *
	 * @param IHttpRequest $request
	 * @param IController $handler
	 * @return boolean <code>true</code> if the execution chain should proceed with the
	 * next interceptor or the handler itself. Else, Dispatcher assumes
	 * that this interceptor has already dealt with the response itself.
	 */
	public function preHandle(IHttpRequest $request, IController $handler) {
		$this->preHandleMethodInvocator = $this->initMethodInvocator('preHandle');
		$this->preHandleMethodInvocator->invokeHandlerMethod($request, array('Bee\MVC\IController' => $handler));
	}

	/**
	 * Enter description here...
	 *
	 * @param IHttpRequest $request
	 * @param IController $handler
	 * @param ModelAndView $mav
	 * @return void
	 */
	public function postHandle(IHttpRequest $request, IController $handler = null, ModelAndView $mav) {
		$this->postHandleMethodInvocator = $this->initMethodInvocator('postHandle');
		$this->postHandleMethodInvocator->invokeHandlerMethod($request, array(
				'Bee\MVC\IController' => $handler,
				'Bee\MVC\ModelAndView' => $mav
		));
	}

	/**
	 * Enter description here...
	 *
	 * @param IHttpRequest $request
	 * @param IController $handler
	 * @param Exception $ex
	 * @return void
	 */
	public function afterCompletion(IHttpRequest $request, IController $handler = null, Exception $ex) {
		$this->afterCompletionMethodInvocator = $this->initMethodInvocator('afterCompletion');
		$this->afterCompletionMethodInvocator->invokeHandlerMethod($request, array(
				'Bee\MVC\IController' => $handler,
				'Exception' => $ex
		));
	}

	/**
	 * @throws Exception
	 * @return string
	 */
	public function getDefaultMethodName() {
		throw new Exception('not implemented: default method name should set in methodInvocators');
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
	 * @param $prefix
	 * @return AnnotationBasedInvocator
	 */
	private function initMethodInvocator($prefix) {
		$instance = new AnnotationBasedInvocator();
		$instance->setDelegatingHandler($this);
		$instance->setBeeContext($this->context);
		$instance->setRequiredMethodPrefix($prefix);
		$instance->setDefaultMethodName($prefix . 'Default');
		return $instance;
	}

	/**
	 * @param IContext $context
	 */
	public function setBeeContext(IContext $context) {
		$this->context = $context;
	}
}