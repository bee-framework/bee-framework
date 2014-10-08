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
use Bee\MVC\IHttpRequest;

/**
 * The multiaction controller handles requests by delegating to method calls on a delegate object. The methods to be used are
 * determined by the method name resolver.
 * 
 * A handler method must take at least one argument, type-hinted to Bee\MVC\IHttpRequest, and return a Bee\MVC\ModelAndView.
 * todo: update phpdoc
 *
 * @see IHttpRequest
 * @see ModelAndView
 * 
 * @author Benjamin Hartmann
 */
interface Bee_MVC_Controller_MultiActionMultiDelegates_IDelegateResolver {

	/**
	 * @param IHttpRequest $request
	 * @return mixed
	 */
    public function resolveDelegate(IHttpRequest $request);

	/**
	 * @return mixed
	 */
    public function getDefaultDelegate();

}