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
use Exception;

/**
 * Enter description here...
 *
 * @see Bee\MVC\Dispatcher
 * 
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
interface IHandlerInterceptor {
	
	/**
	 * Enter description here...
	 *
	 * @param IHttpRequest $request
	 * @param IController $handler
	 * @return boolean <code>true</code> if the execution chain should proceed with the
	 * next interceptor or the handler itself. Else, Dispatcher assumes
	 * that this interceptor has already dealt with the response itself.
	 */
	public function preHandle(IHttpRequest $request, IController $handler);


	/**
	 * Enter description here...
	 *
	 * @param IHttpRequest $request
	 * @param IController $handler
	 * @param ModelAndView $mav
	 * @return void
	 */
	public function postHandle(IHttpRequest $request, IController $handler = null, ModelAndView $mav);

	
	/**
	 * Enter description here...
	 *
	 * @param IHttpRequest $request
	 * @param IController $handler
	 * @param Exception $ex
	 * @return void
	 */
	public function afterCompletion(IHttpRequest $request, IController $handler = null, Exception $ex);
}
