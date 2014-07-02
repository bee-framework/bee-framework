<?php

namespace Bee\MVC;
use Bee_MVC_HttpRequest;


/**
 * Class DefaultRequestBuilder
 * @package Bee\MVC
 */
class DefaultRequestBuilder implements IRequestBuilder {

	/**
	 * @return \Bee_MVC_IHttpRequest
	 */
	public function buildRequestObject() {
		return new Bee_MVC_HttpRequest();
	}
}