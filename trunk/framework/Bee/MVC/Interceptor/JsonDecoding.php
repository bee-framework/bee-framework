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
 * Decodes the JSON string from a request parameter ('json' by default) and adds the contents of the decoded structure as request
 * parameters.
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class Bee_MVC_Interceptor_JsonDecoding implements Bee_MVC_IHandlerInterceptor {
	
	const DEFAULT_JSON_PARAM_NAME = 'json';

	private $jsonParamName = self::DEFAULT_JSON_PARAM_NAME;
	
	public function preHandle(Bee_MVC_IHttpRequest $request, Bee_MVC_IController $handler) {
		if($request instanceof Bee_MVC_HttpRequest) {
			$jsonParam = $request->getParameter($this->jsonParamName);
//			if(array_key_exists($this->jsonParamName, $_REQUEST)) {
			if(Bee_Utils_Strings::hasText($jsonParam)) {
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


	
	public function postHandle(Bee_MVC_IHttpRequest $request, Bee_MVC_IController $handler = null, Bee_MVC_ModelAndView $mav) {
		
	}

	

	public function afterCompletion(Bee_MVC_IHttpRequest $request, Bee_MVC_IController $handler = null, Exception $ex) {
		
	}
}