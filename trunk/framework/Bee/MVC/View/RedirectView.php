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
use Bee\MVC\IHttpStatusCodes;
use Bee\MVC\IView;
use Bee\Utils\Assert;

/**
 * Class RedirectView
 * @package Bee\MVC\View
 */
class RedirectView implements IView, IHttpStatusCodes {

	const MODEL_KEY_REDIRECT_URL = 'redirectUrl';
	const MODEL_KEY_GET_PARAMS = 'getParams';

	/**
	 * @var int
	 */
	private $statusCode = self::HTTP_REDIRECT_SEE_OTHER;

	public function getContentType() {
		return false;
	}

	/**
	 * @param int $statusCode
	 */
	public function setStatusCode($statusCode) {
		$this->statusCode = $statusCode;
	}

	/**
	 * @return int
	 */
	public function getStatusCode() {
		return $this->statusCode;
	}

	public function render(array $model = array()) {
		$redirectUrl = $model[self::MODEL_KEY_REDIRECT_URL];
		Assert::hasText($redirectUrl);
		if (array_key_exists(self::MODEL_KEY_GET_PARAMS, $model)) {
			$params = $model[self::MODEL_KEY_GET_PARAMS];
			Assert::isTrue(is_array($params), 'Bee\MVC\View\RedirectView: getParams must be an array');
			$redirectUrl .= (strpos($redirectUrl, '?') !== false ? '&' : '?') . http_build_query($params);
		}
		header('Location: ' . $redirectUrl, true, $this->getStatusCode());
	}
}