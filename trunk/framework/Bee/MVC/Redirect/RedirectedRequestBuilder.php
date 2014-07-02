<?php

namespace Bee\MVC\Redirect;
use Bee\MVC\DefaultRequestBuilder;

/**
 * Class RedirectedRequestBuilder - restores a previously stored request from the session if applicable, or creates a
 * default request otherwise.
 *
 * @package Bee\MVC\Redirect
 */
class RedirectedRequestBuilder extends DefaultRequestBuilder {

	private $requestIdParamName = 'storedRequestId';

	/**
	 * @param string $requestIdParamName
	 */
	public function setRequestIdParamName($requestIdParamName) {
		$this->requestIdParamName = $requestIdParamName;
	}

	/**
	 * @return string
	 */
	public function getRequestIdParamName() {
		return $this->requestIdParamName;
	}

	/**
	 * @return \Bee_MVC_IHttpRequest
	 */
	public function buildRequestObject() {
		if(array_key_exists($this->requestIdParamName, $_GET)) {
			$id = $_GET[$this->requestIdParamName];
			if(array_key_exists($id, $_SESSION) && ($storage = $_SESSION[$id]) instanceof AbstractRedirectStorage) {
				/** @var AbstractRedirectStorage $storage */
				return $storage->restoreRequestObject($this);
			}
			$this->throwNotFoundException($id);
		}
		return parent::buildRequestObject();
	}

	/**
	 * @param $id
	 * @throws \Exception
	 */
	protected function throwNotFoundException($id) {
		throw new \Exception('Stored request with ID ' . $id . ' not found');
	}
}