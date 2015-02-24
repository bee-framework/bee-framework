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
use Bee\MVC\IHttpRequest;
use Bee\Utils\HashManager;

/**
 * Class AbstractRedirectStorage
 * @package Bee\MVC\Redirect
 */
abstract class AbstractRedirectStorage {

	/**
	 * @var string
	 */
	private $id = false;

	/**
	 * @return string
	 */
	public function getId() {
		if(!$this->id) {
			$this->id = HashManager::createHash();
		}
		return $this->id;
	}

	/**
	 * @param array $model
	 * @return string
	 */
	public final function storeData(array $model = array()) {
		$this->doStoreData($model);
		$_SESSION[$this->getId()] = $this;
		return $this->getId();
	}

	/**
	 * @param array $model
	 * @return mixed
	 */
	abstract protected function doStoreData(array $model);

	/**
	 * @param RedirectedRequestBuilder $requestBuilder
	 * @return IHttpRequest
	 */
	abstract public function restoreRequestObject(RedirectedRequestBuilder $requestBuilder);
}