<?php

namespace Bee\MVC\Redirect;
use Bee\Utils\HashManager;
use Bee_MVC_IHttpRequest;

/**
 * Class AbstractRedirectStorage
 * @package Bee\MVC\Redirect
 */
abstract class AbstractRedirectStorage {

	/**
	 * @var string
	 */
	private $id = false;

	/**
	 * @return string
	 */
	public function getId() {
		if(!$this->id) {
			$this->id = HashManager::createHash();
		}
		return $this->id;
	}

	/**
	 * @param array $model
	 * @return string
	 */
	public final function storeData(array $model = array()) {
		$this->doStoreData($model);
		$_SESSION[$this->getId()] = $this;
		return $this->getId();
	}

	/**
	 * @param array $model
	 * @return mixed
	 */
	abstract protected function doStoreData(array $model);

	/**
	 * @return \Bee_MVC_IHttpRequest
	 */
	abstract public function restoreRequestObject();
}