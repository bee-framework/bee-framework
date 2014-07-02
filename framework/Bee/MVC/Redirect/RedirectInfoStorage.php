<?php

namespace Bee\MVC\Redirect;
use Bee_MVC_HttpRequest;
use Bee_MVC_IHttpRequest;


/**
 * Class RedirectInfoStorage
 * @package Bee\MVC\View
 */
class RedirectInfoStorage extends AbstractRedirectStorage {

	/**
	 * @var array
	 */
	private $storeModelKeys = array();

	/**
	 * @var array
	 */
	private $storedData;

	/**
	 * @param array $storeModelKeys
	 */
	public function setStoreModelKeys(array $storeModelKeys) {
		$this->storeModelKeys = $storeModelKeys;
	}

	/**
	 * @return array
	 */
	public function getStoreModelKeys() {
		return $this->storeModelKeys;
	}

	/**
	 * @param array $model
	 * @return mixed
	 */
	protected function doStoreData(array $model = array()) {
		$this->storedData = array_intersect_key($model, array_fill_keys($this->storeModelKeys, true));
	}

	/**
	 * @return \Bee_MVC_IHttpRequest
	 */
	public function restoreRequestObject() {
		$request = new Bee_MVC_HttpRequest();
		$request->addParameters($this->storedData);
		return $request;
	}
}