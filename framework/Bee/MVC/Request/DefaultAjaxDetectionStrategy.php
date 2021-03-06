<?php
namespace Bee\MVC\Request;
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

/**
 * Class DefaultAjaxDetectionStrategy
 * @package Bee\MVC\Request
 */
class DefaultAjaxDetectionStrategy implements IAjaxDetectionStrategy {

	/**
	 * Determine whether the given request represents an AJAX request or a regular GET / POSTback.
	 * @param IHttpRequest $request
	 * @return mixed
	 */
	public function isAjaxRequest(IHttpRequest $request) {
		return $request->getHeader('X-Requested-With') == 'XMLHttpRequest';
	}
}