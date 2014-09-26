<?php

namespace Bee\MVC\View;
use Bee\MVC\Redirect\AbstractRedirectStorage;
use Bee_MVC_View_Redirect;

/**
 * Class RequestStoringRedirectView
 * @package Bee\MVC\View
 */
class RequestStoringRedirectView extends \Bee_MVC_View_Redirect {

	/**
	 * @var string
	 */
	private $requestIdParamName = 'requestId';

	/**
	 * @var AbstractRedirectStorage[]
	 */
	private $stores = array();

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
	 * @param array $stores
	 */
	public function setStores(array $stores) {
		$this->stores = $stores;
	}

	/**
	 * @return array
	 */
	public function getStores() {
		return $this->stores;
	}

	/**
	 * @param array $model
	 */
	public function render(array $model = array()) {
		$this->augmentModel($model);
		$getParams = array_key_exists(self::MODEL_KEY_GET_PARAMS, $model) ? $model[self::MODEL_KEY_GET_PARAMS] : array();
		foreach($this->stores as $paramName => $store) {
			$getParams[$paramName] = $store->storeData($model);
		}
		$model[self::MODEL_KEY_GET_PARAMS] = $getParams;
		parent::render($model);
	}

	/**
	 * Extension point for subclasses
	 * @param array $model
	 */
	protected function augmentModel(array &$model) {
		// do nothing by default
	}
}