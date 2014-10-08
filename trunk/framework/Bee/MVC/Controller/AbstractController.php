<?php
namespace Bee\MVC\Controller;
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
use Bee\MVC\IHttpRequest;
use Bee\MVC\ModelAndView;

/**
 * Class AbstractController
 */
abstract class AbstractController implements IController {

	/**
	 * Enter description here...
	 *
	 * @todo: is it correct that this always returns a MAV?
	 * @param IHttpRequest $request
	 * @return ModelAndView
	 */
	public final function handleRequest(IHttpRequest $request) {
		$this->init();
		return $this->handleRequestInternally($request);
	}

	/**
	 * Enter description here...
	 *
	 * @return void
	 */
	protected abstract function init();


	/**
	 * Enter description here...
	 *
	 * @param IHttpRequest $request
	 * @return ModelAndView
	 */
	protected abstract function handleRequestInternally(IHttpRequest $request);
}