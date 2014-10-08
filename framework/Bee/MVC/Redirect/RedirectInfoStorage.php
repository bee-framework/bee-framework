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

/**
 * Class RedirectInfoStorage
 * @package Bee\MVC\View
 */
class RedirectInfoStorage extends AbstractRedirectStorage {

	/**
	 * @var array
	 */
	private $storeModelKeys = array();

	/**
	 * @var array
	 */
	private $storedData;

	/**
	 * @param array $storeModelKeys
	 */
	public function setStoreModelKeys(array $storeModelKeys) {
		$this->storeModelKeys = $storeModelKeys;
	}

	/**
	 * @return array
	 */
	public function getStoreModelKeys() {
		return $this->storeModelKeys;
	}

	/**
	 * @param array $model
	 * @return mixed
	 */
	protected function doStoreData(array $model = array()) {
		$this->storedData = array_intersect_key($model, array_fill_keys($this->storeModelKeys, true));
	}

	/**
	 * @param RedirectedRequestBuilder $requestBuilder
	 * @return IHttpRequest
	 */
	public function restoreRequestObject(RedirectedRequestBuilder $requestBuilder) {
		$request = new HttpRequest();
		$request->addParameters($this->storedData);
		return $requestBuilder->massageConstructedRequest($request);
	}
}