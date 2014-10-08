<?php
namespace Bee\MVC\Interceptor;
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
use Bee\MVC\HttpRequest;
use Bee\MVC\IController;
use Bee\MVC\IHandlerInterceptor;
use Bee\MVC\IHttpRequest;
use Bee\MVC\ModelAndView;
use Bee\Utils\Assert;
use Exception;

/**
 * Enter description here...
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 * @author Benjamin Hartmann
 */
class StripSlashesInterceptor implements IHandlerInterceptor {

	/**
	 * @param IHttpRequest $request
	 * @param IController $handler
	 * @return bool
	 */
	public function preHandle(IHttpRequest $request, IController $handler) {
		Assert::isInstanceOf('Bee\MVC\HttpRequest', $request);
		/** @var HttpRequest $request */
		$params = array_map(array($this, 'arrayMapCallback'), $request->getParamArray());
		$request->addParameters($params);
		return true;
	}

	/**
	 * @param $param
	 * @return array|string
	 */
	private function arrayMapCallback($param) {
		if(is_string($param)) {
			$param = stripslashes($param);
		} else if(is_array($param)) {
			$param = array_map(array($this, 'arrayMapCallback'), $param);
		}
		return $param;
	}

	/**
	 * @param IHttpRequest $request
	 * @param IController $handler
	 * @param ModelAndView $mav
	 */
	public function postHandle(IHttpRequest $request, IController $handler = null, ModelAndView $mav) {
		
	}

	/**
	 * @param IHttpRequest $request
	 * @param IController $handler
	 * @param Exception $ex
	 */
	public function afterCompletion(IHttpRequest $request, IController $handler = null, Exception $ex) {
		
	}
}