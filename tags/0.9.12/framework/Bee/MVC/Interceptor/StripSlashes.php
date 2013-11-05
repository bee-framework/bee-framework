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
 * @author Michael Plomer <michael.plomer@iter8.de>
 * @author Benjamin Hartmann
 */
class Bee_MVC_Interceptor_StripSlashes implements Bee_MVC_IHandlerInterceptor {
	
	public function preHandle(Bee_MVC_IHttpRequest $request, Bee_MVC_IController $handler) {
		$params = array_map(array($this, 'arrayMapCallback'), $request->getParamArray());
		$request->addParameters($params);
		return true;
	}
	
	private function arrayMapCallback($param) {
		if(is_string($param)) {
			$param = stripslashes($param);
		} else if(is_array($param)) {
			$param = array_map(array($this, 'arrayMapCallback'), $param);
		}
		return $param;
	}
	
	public function postHandle(Bee_MVC_IHttpRequest $request, Bee_MVC_IController $handler, Bee_MVC_ModelAndView $mav) {
		
	}

	public function afterCompletion(Bee_MVC_IHttpRequest $request, Bee_MVC_IController $handler, Exception $ex) {
		
	}
}
?>