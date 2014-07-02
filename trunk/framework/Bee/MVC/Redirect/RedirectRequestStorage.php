<?php

namespace Bee\MVC\Redirect;
use Bee_MVC_IHttpRequest;
use Bee_Utils_Env;


/**
 * Class RedirectRequestStorage
 * @package Bee\MVC\View
 */
class RedirectRequestStorage extends AbstractRedirectStorage {

	/**
	 * @var array
	 */
	private $getParams;

	/**
	 * @var array
	 */
	private $postParams;

	/**
	 * @var array
	 */
	private $requestParams;

	/**
	 * @var array
	 */
	private $serverVars;

	/**
	 * @var array
	 */
	private $headers;

	/**
	 * @param array $model
	 * @return mixed
	 */
	protected function doStoreData(array $model = array()) {
		$this->getParams = $_GET;
		$this->postParams = $_POST;
		$this->requestParams = $_REQUEST;
		$this->serverVars = $_SERVER;
		$this->headers = Bee_Utils_Env::getRequestHeaders();
	}

	/**
	 * @return \Bee_MVC_IHttpRequest
	 */
	public function restoreRequestObject() {
		$_GET = $this->getParams;
		$_POST = $this->postParams;
		$_REQUEST = $this->requestParams;
		$_SERVER = $this->serverVars;
		return new \Bee_MVC_HttpRequest(null, null, null, $this->headers);
	}
}