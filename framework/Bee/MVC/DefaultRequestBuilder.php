<?php
namespace Bee\MVC;
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
use Bee\MVC\Request\DefaultAjaxDetectionStrategy;
use Bee\MVC\Request\IAjaxDetectionStrategy;
use Bee_MVC_HttpRequest;

/**
 * Class DefaultRequestBuilder
 * @package Bee\MVC
 */
class DefaultRequestBuilder implements IRequestBuilder {

	/**
	 * @var IAjaxDetectionStrategy
	 */
	private $ajaxDetectionStrategy;

	/**
	 * @return \Bee_MVC_IHttpRequest
	 */
	public function buildRequestObject() {
		return $this->massageConstructedRequest(new Bee_MVC_HttpRequest());
	}

	/**
	 * @param Bee_MVC_HttpRequest $request
	 * @return Bee_MVC_HttpRequest
	 */
	public function massageConstructedRequest(Bee_MVC_HttpRequest $request) {
		$request->setAjax($this->getAjaxDetectionStrategy()->isAjaxRequest($request));
		return $request;
	}

	/**
	 * @param IAjaxDetectionStrategy $ajaxDetectionStrategy
	 */
	public function setAjaxDetectionStrategy(IAjaxDetectionStrategy $ajaxDetectionStrategy) {
		$this->ajaxDetectionStrategy = $ajaxDetectionStrategy;
	}

	/**
	 * @return IAjaxDetectionStrategy
	 */
	public function getAjaxDetectionStrategy() {
		if(is_null($this->ajaxDetectionStrategy)) {
			$this->ajaxDetectionStrategy = new DefaultAjaxDetectionStrategy();
		}
		return $this->ajaxDetectionStrategy;
	}
}