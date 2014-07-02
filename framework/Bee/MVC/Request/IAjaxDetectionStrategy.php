<?php

namespace Bee\MVC\Request;
use Bee_MVC_IHttpRequest;

/**
 * Interface IAjaxDetectionStrategy
 * @package Bee\MVC\Request
 */
interface IAjaxDetectionStrategy {

	/**
	 * Determine whether the given request represents an AJAX request or a regular GET / POSTback.
	 * @param Bee_MVC_IHttpRequest $request
	 * @return mixed
	 */
	public function isAjaxRequest(Bee_MVC_IHttpRequest $request);
} 