<?php

namespace Test\Mvc;
use Bee_MVC_IHttpRequest;
use Bee_MVC_ModelAndView;
use Test\MiscClass as MC;


/**
 * Class TestDelegate
 * @package Test\Mvc
 */
class ParamTestDelegate {

	/**
	 *
	 * @param int $paramA
	 * @param Bee_MVC_IHttpRequest $request
	 * @param MC $paramB
	 * @param boolean $boolParam
	 * @param MC $testParam
	 * @param boolean $getParam
	 * @return bool
	 * @Bee_MVC_Controller_Multiaction_RequestHandler(httpMethod = "GET", pathPattern = "/**\/testParam/{0}/{2}/{boolParam}")
	 */
	public function handleTestParams($paramA, Bee_MVC_IHttpRequest $request, MC $paramB, $boolParam = false, $testParam = null, $getParam = false) {
		var_dump($paramA);
		var_dump($paramB);
		var_dump($request);
		var_dump($boolParam);
		var_dump($testParam);
		var_dump($getParam);
		return new Bee_MVC_ModelAndView(array('paramA' => $paramA, 'paramB' => $paramB, 'boolParam' => $boolParam), 'testview');
	}

	/**
	 *
	 * @Bee_MVC_Controller_Multiaction_RequestHandler(httpMethod = "GET", pathPattern = "/**\/testParam/{paramA}/")
	 */
	public function handleTestParams2(MC $paramB, Bee_MVC_IHttpRequest $request) {
		var_dump($paramB);
		var_dump($request);
		return true;
	}

	/**
	 * @param int $getParam
	 * @return Bee_MVC_ModelAndView
	 */
	public function handleDefault($getParam = 1000) {
		var_dump($getParam);
		return new Bee_MVC_ModelAndView(array(), 'defaultview');
	}
}