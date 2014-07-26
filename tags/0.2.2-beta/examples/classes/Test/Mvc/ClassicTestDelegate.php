<?php
namespace Test\Mvc;

use Bee_MVC_IHttpRequest;


/**
 * Class ClassicTestDelegate
 * @package Test\Mvc
 */
class ClassicTestDelegate {

	/**
	 *
	 * @Bee_MVC_Controller_Multiaction_RequestHandler(httpMethod = "GET", pathPattern = "/**\/testClassic/**")
	 */
	public function handleTestParams(Bee_MVC_IHttpRequest $request) {
		var_dump($request);
		return true;
	}

} 