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
use Bee\MVC\IController;
use Bee\MVC\IHandlerMapping;
use Bee_Context_Config_IContextAware;
use Bee_IContext;
use Bee_MVC_HandlerExecutionChain;
use Bee_MVC_IHttpRequest;
use Exception;

/**
 * Abstract base class for HandlerMappings
 *
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
abstract class AbstractHandlerMapping implements IHandlerMapping, Bee_Context_Config_IContextAware {

	/**
	 * Enter description here...
	 *
	 * @var Bee_IContext
	 */
	private $context;

	/**
	 * Enter description here...
	 *
	 * @var Bee_IContext
	 */
	private $defaultControllerBeanName;

	/**
	 * Enter description here...
	 *
	 * @var \Bee\MVC\IHandlerInterceptor[]
	 */
	private $interceptors = array();

	/**
	 * @param Bee_IContext $context
	 */
	public function setBeeContext(Bee_IContext $context) {
		$this->context = $context;
	}

	/**
	 * Enter description here...
	 *
	 * @return String
	 */
	public function getDefaultControllerBeanName() {
		return $this->defaultControllerBeanName;
	}

	/**
	 * Enter description here...
	 *
	 * @param String $defaultControllerBeanName
	 * @return void
	 */
	public function setDefaultControllerBeanName($defaultControllerBeanName) {
		$this->defaultControllerBeanName = $defaultControllerBeanName;
	}

	/**
	 * Enter description here...
	 *
	 * @param \Bee\MVC\IHandlerInterceptor[] $interceptors
	 */
	public function setInterceptors(array $interceptors) {
		$this->interceptors = $interceptors;
	}

	/**
	 * Enter description here...
	 *
	 * @return \Bee\MVC\IHandlerInterceptor[]
	 */
	public function getInterceptors() {
		return $this->interceptors;
	}

	/**
	 * @param Bee_MVC_IHttpRequest $request
	 * @return Bee_MVC_HandlerExecutionChain
	 * @throws Exception
	 */
	public function getHandler(Bee_MVC_IHttpRequest $request) {
		$controllerBeanName = $this->getControllerBeanName($request);
		$handlerBean = is_string($controllerBeanName) ?
				$handlerBean = $this->context->getBean($controllerBeanName, 'IController') :
				$controllerBeanName;

		if (!$handlerBean instanceof IController) {
			throw new Exception('Error retrieving handler bean: must be a valid bean name or Bee\MVC\IController instance');
		}
		
		$hec = new Bee_MVC_HandlerExecutionChain($handlerBean);
		$hec->addInterceptors($this->interceptors);
		return $hec;
	}

	/**
	 * Resolves the actual controller bean name (may also return a controller instance directly)
	 * @param Bee_MVC_IHttpRequest $request
	 * @return mixed
	 */
	protected abstract function getControllerBeanName(Bee_MVC_IHttpRequest $request);
}