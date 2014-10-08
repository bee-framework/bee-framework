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
 * Base interface for handler mappings. Used by the Dispatcher.
 * 
 * @see Bee\MVC\Dispatcher
 * 
 * @author Michael Plomer <michael.plomer@iter8.de>
 * @author Benjamin Hartmann
 */
interface IHandlerMapping {

	/**
	 * Based on the current request object, returns a handler execution chain, containing the back controller to be used, as well as any
	 * interceptors applicable to this request.
	 *
	 * @param HttpRequest|IHttpRequest $request
	 * @return HandlerExecutionChain
	 *
	 * @see IController
	 * @see IHandlerInterceptor
	 * @see HandlerExecutionChain
	 */
	public function getHandler(IHttpRequest $request);

	/**
	 * Get all configured interceptors
	 * @return IHandlerInterceptor[]
	 */
	public function getInterceptors();
}