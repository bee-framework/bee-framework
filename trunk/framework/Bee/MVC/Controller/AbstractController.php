<?php
namespace Bee\MVC\Controller;

use Bee\MVC\IController;
use Bee_MVC_IHttpRequest;

/**
 * Class AbstractController
 */
abstract class AbstractController implements IController {

	/**
	 * Enter description here...
	 *
	 * @todo: is it correct that this always returns a MAV?
	 * @param Bee_MVC_IHttpRequest $request
	 * @return \Bee_MVC_ModelAndView
	 */
	public final function handleRequest(Bee_MVC_IHttpRequest $request) {
		$this->init();
		return $this->handleRequestInternally($request);
	}

	/**
	 * Enter description here...
	 *
	 * @return void
	 */
	protected abstract function init();


	/**
	 * Enter description here...
	 *
	 * @param Bee_MVC_IHttpRequest $request
	 * @return \Bee_MVC_ModelAndView
	 */
	protected abstract function handleRequestInternally(Bee_MVC_IHttpRequest $request);
}