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
	 * @param Bee_MVC_IHttpRequest $request
	 * @return bool
	 */
	public function handleTestParams(Bee_MVC_IHttpRequest $request) {
//		var_dump($request);
		return new \Bee_MVC_ModelAndView(array('delegateName' => get_class($this)), 'testview');
	}
kk
} 