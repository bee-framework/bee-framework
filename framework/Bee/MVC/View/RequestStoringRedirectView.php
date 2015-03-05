<?php
namespace Bee\MVC\View;
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
use Bee\MVC\Redirect\AbstractRedirectStorage;

/**
 * Class RequestStoringRedirectView
 * @package Bee\MVC\View
 */
class RequestStoringRedirectView extends RedirectView {

	/**
	 * @var string
	 */
	private $requestIdParamName = 'requestId';

	/**
	 * @var AbstractRedirectStorage[]
	 */
	private $stores = array();

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
	 * @param array $stores
	 */
	public function setStores(array $stores) {
		$this->stores = $stores;
	}

	/**
	 * @return array
	 */
	public function getStores() {
		return $this->stores;
	}

	/**
	 * @param array $model
	 */
	public function render(array $model = array()) {
		$this->augmentModel($model);
		$getParams = array_key_exists(self::MODEL_KEY_GET_PARAMS, $model) ? $model[self::MODEL_KEY_GET_PARAMS] : array();
		foreach($this->stores as $paramName => $store) {
			$getParams[$paramName] = $store->storeData($model);
		}
		$model[self::MODEL_KEY_GET_PARAMS] = $getParams;
		parent::render($model);
	}

	/**
	 * Extension point for subclasses
	 * @param array $model
	 */
	protected function augmentModel(array &$model) {
		// do nothing by default
	}
}