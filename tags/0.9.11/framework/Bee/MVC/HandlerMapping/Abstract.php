<?php
/*
 * Copyright 2008-2010 the original author or authors.
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

/**
 * Abstract base class for HandlerMappings
 *
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
abstract class Bee_MVC_HandlerMapping_Abstract implements Bee_MVC_IHandlerMapping, Bee_Context_Config_IContextAware {

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
	 * @var array
	 */
	private $interceptors = array();

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
	 * @param array $interceptors
	 */
	public function setInterceptors(array $interceptors) {
		$this->interceptors = $interceptors;
	}

	/**
	 * Enter description here...
	 *
	 * @return array
	 */
	public function getInterceptors() {
		return $this->interceptors;
	}

	public function getHandler(Bee_MVC_IHttpRequest $request) {
		$controllerBeanName = $this->getControllerBeanName($request);
		$handlerBean = is_string($controllerBeanName) ?
				$handlerBean = $this->context->getBean($controllerBeanName, 'Bee_MVC_IController') :
				$controllerBeanName;

		if (!$handlerBean instanceof Bee_MVC_IController) {
			throw new Exception('Error retrieving handler bean: must be a valid bean name or Bee_MVC_IController instance');
		}
		
		$hec = new Bee_MVC_HandlerExecutionChain($handlerBean);
		$hec->addInterceptors($this->interceptors);
		return $hec;
	}

	protected abstract function getControllerBeanName(Bee_MVC_IHttpRequest $request);
}