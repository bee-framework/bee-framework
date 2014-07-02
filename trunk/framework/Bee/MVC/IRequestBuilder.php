<?php

namespace Bee\MVC;

/**
 * Interface IRequestBuilder
 *
 * @package Bee\MVC
 */
interface IRequestBuilder {

	/**
	 * @return \Bee_MVC_IHttpRequest
	 */
	public function buildRequestObject();
} 