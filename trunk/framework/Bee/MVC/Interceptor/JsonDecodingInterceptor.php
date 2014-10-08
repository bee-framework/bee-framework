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
use Bee\Utils\Strings;
use Exception;

/**
 * Decodes the JSON string from a request parameter ('json' by default) and adds the contents of the decoded structure as request
 * parameters.
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class JsonDecodingInterceptor implements IHandlerInterceptor {
	
	const DEFAULT_JSON_PARAM_NAME = 'json';

	private $jsonParamName = self::DEFAULT_JSON_PARAM_NAME;

	/**
	 * @param IHttpRequest $request
	 * @param IController $handler
	 * @return bool
	 */
	public function preHandle(IHttpRequest $request, IController $handler) {
		if($request instanceof HttpRequest) {
			$jsonParam = $request->getParameter($this->jsonParamName);
//			if(array_key_exists($this->jsonParamName, $_REQUEST)) {
			if(Strings::hasText($jsonParam)) {
				$decoded = json_decode($jsonParam, true);
				$request->setParameter($this->jsonParamName, null);
				$request->addParameters($decoded);
//				unset($_REQUEST[$this->jsonParamName]);				
//				foreach ($decoded as $propName => $propValue) {
//					$_REQUEST[$propName] = $propValue;
//				}
			}
		}
		return true;
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