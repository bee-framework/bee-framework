<?php
namespace Bee\MVC;
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

/**
 * Enter description here...
 *
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
final class HandlerExecutionChain {
	
	/**
	 * Enter description here...
	 *
	 * @var \Bee\MVC\IController
	 */
	private $handler;

	/**
	 * Enter description here...
	 *
	 * @var IHandlerInterceptor[]
	 */
	private $interceptors = array();

	/**
	 * Enter description here...
	 *
	 * @param IController $handler
	 */
	public function __construct(IController $handler) {
		$this->handler = $handler;
	}

	/**
	 * Enter description here...
	 *
	 * @param IHandlerInterceptor $interceptor
	 * @return void
	 */
	public function addInterceptor(IHandlerInterceptor $interceptor) {
		array_push($this->interceptors, $interceptor);
	}

	/**
	 * Enter description here...
	 *
	 * @param IHandlerInterceptor[] $interceptors
	 * @return void
	 */
	public function addInterceptors(array $interceptors) {
		$this->interceptors = array_merge($this->interceptors, $interceptors);
	}

	/**
	 * Enter description here...
	 *
	 * @return IController
	 */
	public function getHandler() {
		return $this->handler;
	}

	/**
	 * Enter description here...
	 *
	 * @return IHandlerInterceptor[]
	 */
	public function getInterceptors() {
		return $this->interceptors;
	}
}