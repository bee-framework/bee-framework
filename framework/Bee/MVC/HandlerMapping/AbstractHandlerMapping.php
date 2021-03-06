<?php
namespace Bee\MVC\HandlerMapping;
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
use Bee\Context\Config\TContextAware;
use Bee\IContext;
use Bee\MVC\HandlerExecutionChain;
use Bee\MVC\IController;
use Bee\MVC\IHandlerInterceptor;
use Bee\MVC\IHandlerMapping;
use Bee\MVC\IHttpRequest;
use Exception;

/**
 * Abstract base class for HandlerMappings
 *
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
abstract class AbstractHandlerMapping implements IHandlerMapping, IContextAware {
    use TContextAware;

	/**
	 * Enter description here...
	 *
	 * @var string
	 */
	private $defaultControllerBeanName;

	/**
	 * Enter description here...
	 *
	 * @var IHandlerInterceptor[]
	 */
	private $interceptors = array();

	/**
	 * Enter description here...
	 *
	 * @return string
	 */
	public function getDefaultControllerBeanName() {
		return $this->defaultControllerBeanName;
	}

	/**
	 * Enter description here...
	 *
	 * @param string $defaultControllerBeanName
	 * @return void
	 */
	public function setDefaultControllerBeanName($defaultControllerBeanName) {
		$this->defaultControllerBeanName = $defaultControllerBeanName;
	}

	/**
	 * Enter description here...
	 *
	 * @param IHandlerInterceptor[] $interceptors
	 */
	public function setInterceptors(array $interceptors) {
		$this->interceptors = $interceptors;
	}

	/**
	 * Enter description here...
	 *
	 * @return IHandlerInterceptor[]
	 */
	public function getInterceptors() {
		return $this->interceptors;
	}

	/**
	 * @param IHttpRequest $request
	 * @return HandlerExecutionChain
	 * @throws Exception
	 */
	public function getHandler(IHttpRequest $request) {
		$controllerBeanName = $this->getControllerBeanName($request);
		$handlerBean = is_string($controllerBeanName) ?
				$handlerBean = $this->context->getBean($controllerBeanName, 'Bee\MVC\IController') :
				$controllerBeanName;

		if (!$handlerBean instanceof IController) {
			throw new Exception('Error retrieving handler bean: must be a valid bean name or Bee\MVC\IController instance');
		}
		
		$hec = new HandlerExecutionChain($handlerBean);
		$hec->addInterceptors($this->filterInterceptors($request));
		return $hec;
	}

	/**
	 * Default implementation returns unfiltered view of the interceptors collection.
	 * @param IHttpRequest $request
	 * @return IHandlerInterceptor[]
	 */
	protected function filterInterceptors(IHttpRequest $request) {
		return $this->interceptors;
	}

	/**
	 * Resolves the actual controller bean name (may also return a controller instance directly)
	 * @param IHttpRequest $request
	 * @return mixed
	 */
	protected abstract function getControllerBeanName(IHttpRequest $request);
}