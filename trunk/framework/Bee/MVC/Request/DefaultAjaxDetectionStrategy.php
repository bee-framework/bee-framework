<?php

namespace Bee\MVC\Request;
use Bee_MVC_IHttpRequest;


/**
 * Class DefaultAjaxDetectionStrategy
 * @package Bee\MVC\Request
 */
class DefaultAjaxDetectionStrategy implements IAjaxDetectionStrategy {

	/**
	 * Determine whether the given request represents an AJAX request or a regular GET / POSTback.
	 * @param Bee_MVC_IHttpRequest $request
	 * @return mixed
	 */
	public function isAjaxRequest(Bee_MVC_IHttpRequest $request) {
		return $request->getHeader('X-Requested-With') == 'XMLHttpRequest';
	}
}