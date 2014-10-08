<?php
namespace Test\Mvc;

use Bee\MVC\IHttpRequest;
use Bee\MVC\ModelAndView;


/**
 * Class ClassicTestDelegate
 * @package Test\Mvc
 */
class ClassicTestDelegate {

	/**
	 *
	 * @Bee_MVC_Controller_Multiaction_RequestHandler(httpMethod = "GET", pathPattern = "/**\/testClassic/**")
	 * @param IHttpRequest $request
	 * @return bool
	 */
	public function handleTestParams(IHttpRequest $request) {
//		var_dump($request);
		return new ModelAndView(array('delegateName' => get_class($this)), 'testview');
	}
}