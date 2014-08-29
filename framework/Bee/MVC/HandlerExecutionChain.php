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
 * Enter description here...
 *
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
final class Bee_MVC_HandlerExecutionChain {
	
	/**
	 * Enter description here...
	 *
	 * @var Bee_MVC_IController
	 */
	private $handler;

	/**
	 * Enter description here...
	 *
	 * @var Bee_MVC_IHandlerInterceptor[]
	 */
	private $interceptors = array();

	/**
	 * Enter description here...
	 *
	 * @param Bee_MVC_IController $handler
	 * @internal param \Bee_MVC_IController $controller
	 */
	public function __construct(Bee_MVC_IController $handler) {
		$this->handler = $handler;
	}

	/**
	 * Enter description here...
	 *
	 * @param Bee_MVC_IHandlerInterceptor $interceptor
	 * @return void
	 */
	public function addInterceptor(Bee_MVC_IHandlerInterceptor $interceptor) {
		array_push($this->interceptors, $interceptor);
	}

	/**
	 * Enter description here...
	 *
	 * @param Bee_MVC_IHandlerInterceptor[] $interceptors
	 * @return void
	 */
	public function addInterceptors(array $interceptors) {
		$this->interceptors = array_merge($this->interceptors, $interceptors);
	}

	/**
	 * Enter description here...
	 *
	 * @return Bee_MVC_IController
	 */
	public function getHandler() {
		return $this->handler;
	}

	/**
	 * Enter description here...
	 *
	 * @return Bee_MVC_IHandlerInterceptor[]
	 */
	public function getInterceptors() {
		return $this->interceptors;
	}
}