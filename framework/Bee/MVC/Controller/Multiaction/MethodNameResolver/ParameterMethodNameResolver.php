<?php
namespace Bee\MVC\Controller\Multiaction\MethodNameResolver;
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
use Bee\MVC\Controller\Multiaction\AbstractDelegatingHandlerHolder;
use Bee\MVC\Controller\Multiaction\IMethodNameResolver;
use Bee\MVC\IHttpRequest;

/**
 * Method name resolver implementation that uses the value of a request parameter ('action' by default) as the name of the handler method.
 *
 * @see Bee\MVC\Controller\MultiAction
 * @see Bee\MVC\Controller\Multiaction\IMethodNameResolver
 * 
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class ParameterMethodNameResolver extends AbstractDelegatingHandlerHolder implements IMethodNameResolver {

	const DEFAULT_PARAM_NAME = 'action';

	/**
	 * Enter description here...
	 *
	 * @var string
	 */
	private $paramName;

	/**
	 * Enter description here...
	 *
	 * @param string $paramName
	 */
	public function setParamName($paramName) {
		$this->paramName = $paramName;
	}

	/**
	 * @return string
	 */
	public function getParamName() {
		return is_null($this->paramName) ? self::DEFAULT_PARAM_NAME : $this->paramName;
	}

	/**
	 * @param IHttpRequest $request
	 * @return mixed|String
	 */
	public function getHandlerMethodName(IHttpRequest $request) {
		return $request->getParameter($this->getParamName());
	}
}