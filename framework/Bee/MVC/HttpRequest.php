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

class Bee_MVC_HttpRequest implements Bee_MVC_IHttpRequest {
	
	const SERVER_REQUEST_METHOD = 'REQUEST_METHOD';
	
	/**
	 * Enter description here...
	 *
	 * @var array
	 */	
	private $parameters;

	/**
	 * Enter description here...
	 *
	 * @var array
	 */
	private $parameterNames;

	/**
	 * Enter description here...
	 *
	 * @var String
	 */
	private $pathInfo;
	
	/**
	 * Enter description here...
	 *
	 * @var String
	 */
	private $method;
	
	/**
	 * Enter description here...
	 *
	 * @var array
	 */
	private $headers;
	
	/**
	 * Enter description here...
	 *
	 * @var array
	 */
	private $headerNames;
	
	public function __construct(array $parameters = null, $pathInfo = null, $method = null, array $headers = null) {
		if(is_null($headers)) {
			$headers = Bee_Utils_Env::getRequestHeaders();
		}
		if(is_null($method)) {
			$method = $_SERVER[self::SERVER_REQUEST_METHOD];
		}
		if(is_null($pathInfo)) {
			$pathInfo = Bee_Utils_Env::getPathInfo();
		}
		if(is_null($parameters)) {
			$parameters = $_REQUEST;
		}
		$this->parameters = $parameters;
		$this->pathInfo = $pathInfo;
		$this->method = $method;
		
		$this->headerNames = array_keys($headers);
		$this->headers = array_change_key_case($headers, CASE_UPPER);
	}
	
	public function getPathInfo() {
//		if (is_null($this->pathInfo)) {
//			if(Bee_Utils_Strings::hasText($_SERVER['PATH_INFO'])) {
//				$this->pathInfo = $_SERVER['PATH_INFO'];
//
//			} else if(Bee_Utils_Strings::hasText($_SERVER['ORIG_PATH_INFO'])) {
//				if ($_SERVER['ORIG_PATH_INFO'] == $_SERVER['ORIG_SCRIPT_NAME']) {
//					$this->pathInfo = '';
//				} else {
//					$this->pathInfo = $_SERVER['ORIG_PATH_INFO'];
//				}
//			}
//		}
		return $this->pathInfo;
	}

	public function getMethod() {
		return $this->method;
	}

	/**
	 * Returns the PATH_INFO (i.e. any additional path trailing the actual PHP file)
	 *
	 * @return string
	 */
	public function getParameter($name) {
//		$val = $this->parameters[$name];
//		if(is_array($val)) {
//			$val = $val[0];
//		}
//		return $val;
        return $this->parameters[$name];
	}
	
	public function setParameter($name, $value) {
		if(is_null($value)) {
			unset($this->parameters[$name]);
		} else {
			$this->parameters[$name] = $value;
		}
	}
	
	public function addParameters(array $params) {
		$this->parameterNames = null;
		$this->parameters = array_merge($this->parameters, $params);
	}

	public function getParameterValues($name) {
		$val = $this->parameters[$name];
		if (is_null($val)) {
			return array();
		}
		if(!is_array($val)) {
			$val = array($val);
		}
		return $val;
	}

	public function getParameterNames() {
		if(is_null($this->parameterNames)) {
			$this->parameterNames = array_keys($this->parameters);
		}
		return $this->parameterNames;
	}
			
	public function getHeader($name) {
		return $this->headers[strtoupper($name)];
	}
	
	public function getHeaderNames() {
		if(is_null($this->headerNames)) {
		}
		return $this->headerNames;
	}
	
	public function getParamArray() {
		return $this->parameters;
	}
	
	public static function constructRequest(Bee_MVC_IHttpRequest $request, $pathInfo = null, array $params = null, $method = null) {
		if(!($request instanceof Bee_MVC_HttpRequest)) {
			throw new Exception('Invalid parameter: cannot handle foreign implementations of Bee_MVC_IHttpRequest');
		}
		if(!Bee_Utils_Strings::hasText($pathInfo)) {
			$pathInfo = $request->getPathInfo();
		}
		
		$params = array_merge($request->getParamArray(), (!is_null($params) ? $params : array()));

		if(!Bee_Utils_Strings::hasText($method)) {
			$method = $request->getMethod();
		}
		$headers = array();
		foreach($request->getHeaderNames() as $header) {
			$headers[$header] = $request->getHeader($header);
		}
		return new Bee_MVC_HttpRequest($params, $pathInfo, $method, $headers);
	}
}
?>