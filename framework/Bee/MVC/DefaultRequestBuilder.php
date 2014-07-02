<?php

namespace Bee\MVC;
use Bee\MVC\Request\DefaultAjaxDetectionStrategy;
use Bee\MVC\Request\IAjaxDetectionStrategy;
use Bee_MVC_HttpRequest;

/**
 * Class DefaultRequestBuilder
 * @package Bee\MVC
 */
class DefaultRequestBuilder implements IRequestBuilder {

	/**
	 * @var IAjaxDetectionStrategy
	 */
	private $ajaxDetectionStrategy;

	/**
	 * @return \Bee_MVC_IHttpRequest
	 */
	public function buildRequestObject() {
		return $this->massageConstructedRequest(new Bee_MVC_HttpRequest());
	}

	/**
	 * @param Bee_MVC_HttpRequest $request
	 * @return Bee_MVC_HttpRequest
	 */
	public function massageConstructedRequest(Bee_MVC_HttpRequest $request) {
		$request->setAjax($this->getAjaxDetectionStrategy()->isAjaxRequest($request));
		return $request;
	}

	/**
	 * @param IAjaxDetectionStrategy $ajaxDetectionStrategy
	 */
	public function setAjaxDetectionStrategy(IAjaxDetectionStrategy $ajaxDetectionStrategy) {
		$this->ajaxDetectionStrategy = $ajaxDetectionStrategy;
	}

	/**
	 * @return IAjaxDetectionStrategy
	 */
	public function getAjaxDetectionStrategy() {
		if(is_null($this->ajaxDetectionStrategy)) {
			$this->ajaxDetectionStrategy = new DefaultAjaxDetectionStrategy();
		}
		return $this->ajaxDetectionStrategy;
	}
}