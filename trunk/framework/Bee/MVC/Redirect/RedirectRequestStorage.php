<?php
namespace Bee\MVC\Redirect;
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
use Bee\MVC\IHttpRequest;
use Bee\Utils\Env;

/**
 * Class RedirectRequestStorage
 * @package Bee\MVC\View
 */
class RedirectRequestStorage extends AbstractRedirectStorage {

	/**
	 * @var array
	 */
	private $getParams;

	/**
	 * @var array
	 */
	private $postParams;

	/**
	 * @var array
	 */
	private $requestParams;

	/**
	 * @var array
	 */
	private $serverVars;

	/**
	 * @var array
	 */
	private $headers;

	/**
	 * @param array $model
	 * @return mixed
	 */
	protected function doStoreData(array $model = array()) {
		$this->getParams = $_GET;
		$this->postParams = $_POST;
		$this->requestParams = $_REQUEST;
		$this->serverVars = $_SERVER;
		$this->headers = Env::getRequestHeaders();
	}

	/**
	 * @param RedirectedRequestBuilder $requestBuilder
	 * @return IHttpRequest
	 */
	public function restoreRequestObject(RedirectedRequestBuilder $requestBuilder) {
		$_GET = $this->getParams;
		$_POST = $this->postParams;
		$_REQUEST = $this->requestParams;
		$_SERVER = $this->serverVars;
		return $requestBuilder->massageConstructedRequest(new HttpRequest(null, null, null, $this->headers));
	}
}