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
use Bee\MVC\DefaultRequestBuilder;
use Bee\MVC\IHttpRequest;

/**
 * Class RedirectedRequestBuilder - restores a previously stored request from the session if applicable, or creates a
 * default request otherwise.
 *
 * @package Bee\MVC\Redirect
 */
class RedirectedRequestBuilder extends DefaultRequestBuilder {

	private $requestIdParamName = 'storedRequestId';

	/**
	 * @param string $requestIdParamName
	 */
	public function setRequestIdParamName($requestIdParamName) {
		$this->requestIdParamName = $requestIdParamName;
	}

	/**
	 * @return string
	 */
	public function getRequestIdParamName() {
		return $this->requestIdParamName;
	}

	/**
	 * @return IHttpRequest
	 */
	public function buildRequestObject() {
		if(array_key_exists($this->requestIdParamName, $_GET)) {
			$id = $_GET[$this->requestIdParamName];
			if(array_key_exists($id, $_SESSION) && ($storage = $_SESSION[$id]) instanceof AbstractRedirectStorage) {
				/** @var AbstractRedirectStorage $storage */
				return $storage->restoreRequestObject($this);
			}
			$this->throwNotFoundException($id);
		}
		return parent::buildRequestObject();
	}

	/**
	 * @param $id
	 * @throws \Exception
	 */
	protected function throwNotFoundException($id) {
		throw new \Exception('Stored request with ID ' . $id . ' not found');
	}
}