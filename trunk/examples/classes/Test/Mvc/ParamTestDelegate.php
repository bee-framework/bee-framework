<?php

namespace Test\Mvc;
use Bee_MVC_IHttpRequest;
use Test\MiscClass;


/**
 * Class TestDelegate
 * @package Test\Mvc
 */
class ParamTestDelegate {

	/**
	 *
	 * @Bee_MVC_Controller_Multiaction_RequestHandler(httpMethod = "GET", pathPattern = "/**\/testParam/{paramA}/{paramB}")
	 */
	public function handleTestParams($paramA, MiscClass $paramB, Bee_MVC_IHttpRequest $request) {
		var_dump($paramA);
		var_dump($paramB);
		var_dump($request);
		return true;
	}

	/**
	 *
	 * @Bee_MVC_Controller_Multiaction_RequestHandler(httpMethod = "GET", pathPattern = "/**\/testParam/{paramA}/")
	 */
	public function handleTestParams2(MiscClass $paramB, Bee_MVC_IHttpRequest $request) {
		var_dump($paramB);
		var_dump($request);
		return true;
	}
}