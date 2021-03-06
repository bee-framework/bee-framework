<?php
namespace Bee\MVC\Controller\Multiaction;
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
use Bee\MVC\IDelegatingHandler;
use Bee\MVC\IHttpRequest;

/**
 * Base interface for method name resolvers. Used by the multiaction controller to determine the handler method for the
 * current request.
 *
 * @see Bee\MVC\Controller\MultiAction
 * 
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
interface IMethodNameResolver {

	/**
	 * @param IDelegatingHandler $controller
	 * @return mixed
	 */
	public function setDelegatingHandler(IDelegatingHandler $controller);

	/**
	 * @param IHttpRequest $request
	 * @return string
	 */
	public function getHandlerMethodName(IHttpRequest $request);
}
