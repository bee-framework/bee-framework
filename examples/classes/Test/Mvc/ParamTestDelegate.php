<?php

namespace Test\Mvc;
use Bee\MVC\IHttpRequest;
use Bee\MVC\ModelAndView;
use Test\MiscClass as MC;


/**
 * Class TestDelegate
 * @package Test\Mvc
 */
class ParamTestDelegate {

	/**
	 *
	 * @param int $paramA
	 * @param IHttpRequest $request
	 * @param MC $paramB
	 * @param boolean $boolParam
	 * @param MC $testParam
	 * @param boolean $getParam
	 * @return bool
	 * @Bee_MVC_Controller_Multiaction_RequestHandler(httpMethod = "GET", pathPattern = "/**\/testClassic/**")
	 * @Bee_MVC_Controller_Multiaction_RequestHandler(pathPattern = "/**\/ghi/*\/{0}/{2}/const/{boolParam}", httpMethod="GET")
	 * @Bee_MVC_Controller_Multiaction_RequestHandler(pathPattern = "/**\/{0}/{2}/{boolParam}", httpMethod="POST")
	 */
	public function handleTestParams($paramA = null, IHttpRequest $request, MC $paramB, $boolParam = false, $testParam = null, $getParam = false) {
		echo '<hr/>'. get_class($this) .'::'. __FUNCTION__ .'<hr/>';
//		var_dump($paramA);
//		var_dump($paramB);
//		var_dump($request);
//		var_dump($boolParam);
//		var_dump($testParam);
//		var_dump($getParam);
//		echo '<hr/>';
//		var_dump($this);
		return new ModelAndView(array('paramA' => $paramA, 'paramB' => $paramB, 'boolParam' => $boolParam), 'testview');
	}

	/**
	 *
	 * @param int $paramA
	 * @param IHttpRequest $request
	 * @param MC $paramB
	 * @param boolean $boolParam
	 * @param MC $testParam
	 * @param boolean $getParam
	 * @return bool
	 * @Bee_MVC_Controller_Multiaction_RequestHandler(pathPattern = "/**\/ttt/{0}/{2}/{boolParam}")
	 */
	public function handleTestParams25($paramA, IHttpRequest $request, MC $paramB, $boolParam = false, $testParam = null, $getParam = false) {
		echo '<hr/>'. get_class($this) .'::'. __FUNCTION__ .'<hr/>';
//		var_dump($paramA);
//		var_dump($paramB);
//		var_dump($request);
//		var_dump($boolParam);
//		var_dump($testParam);
//		var_dump($getParam);
//		echo '<hr/>';
//		var_dump($this);
		return new ModelAndView(array('paramA' => $paramA, 'paramB' => $paramB, 'boolParam' => $boolParam), 'testview');
	}

	/**
	 *
	 * @Bee_MVC_Controller_Multiaction_RequestHandler(pathPattern = "/**\/testParam/{paramA}/")
	 * @param MC $paramB
	 * @param IHttpRequest $request
	 * @return bool
	 */
	public function handleTestParams2(MC $paramB, IHttpRequest $request) {
		echo '<hr/>'. get_class($this) .'::'. __FUNCTION__ .'<hr/>';
//		var_dump($paramB);
//		var_dump($request);
		return true;
	}

	/**
	 * @param int $getParam
	 * @return ModelAndView
	 * @Bee_MVC_Controller_Multiaction_RequestHandler(pathPattern = "/**", httpMethod="GET")
	 */
	public function handleDefault($getParam = 1000) {
		echo '<hr/>'. get_class($this) .'::'. __FUNCTION__ .'<hr/>';
//		var_dump($getParam);
		return new ModelAndView(array(), 'defaultview');
	}
}