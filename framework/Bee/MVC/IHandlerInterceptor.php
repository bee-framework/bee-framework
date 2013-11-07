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
 * @see Bee_MVC_Dispatcher
 * 
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
interface Bee_MVC_IHandlerInterceptor {
	
	/**
	 * Enter description here...
	 *
	 * @param Bee_MVC_IHttpRequest $request
	 * @param Bee_MVC_IController $handler
	 * @return boolean <code>true</code> if the execution chain should proceed with the
	 * next interceptor or the handler itself. Else, Dispatcher assumes
	 * that this interceptor has already dealt with the response itself.
	 */
	public function preHandle(Bee_MVC_IHttpRequest $request, Bee_MVC_IController $handler);


	/**
	 * Enter description here...
	 *
	 * @param Bee_MVC_IHttpRequest $request
	 * @param Bee_MVC_IController $handler
	 * @param Bee_MVC_ModelAndView $mav
	 * @return void
	 */
	public function postHandle(Bee_MVC_IHttpRequest $request, Bee_MVC_IController $handler = null, Bee_MVC_ModelAndView $mav);

	
	/**
	 * Enter description here...
	 *
	 * @param Bee_MVC_IHttpRequest $request
	 * @param Bee_MVC_IController $handler
	 * @param Exception $ex
	 * @return void
	 */
	public function afterCompletion(Bee_MVC_IHttpRequest $request, Bee_MVC_IController $handler = null, Exception $ex);
}
