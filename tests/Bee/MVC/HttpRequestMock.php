<?php
namespace Bee\MVC;
use Bee_MVC_IHttpRequest;

/**
 * Class HttpRequestMock
 * @package Bee\MVC
 */
class HttpRequestMock implements Bee_MVC_IHttpRequest {

	/**
	 * @var string
	 */
	private $pathInfo;

	/**
	 * @var string
	 */
	private $method;

	/**
	 * @var array
	 */
	private $parameters = array();

	/**
	 * @var array
	 */
	private $headers = array();

	/**
	 * @var bool
	 */
	private $ajax = false;

	/**
	 * @param $pathInfo
	 * @param $parameters
	 * @param $method
	 * @param $headers
	 * @param $ajax
	 */
	function __construct($pathInfo = '', array $parameters = array(), $method = 'GET', array $headers = array(), $ajax = false) {
		$this->pathInfo = $pathInfo;
		$this->parameters = $parameters;
		$this->method = $method;
		$this->headers = array_change_key_case($headers, CASE_UPPER);
		$this->ajax = $ajax;
	}


	/**
	 * @return string
	 */
	public function getPathInfo() {
		return $this->pathInfo;
	}

	/**
	 * @return string
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasParameter($name) {
		return array_key_exists($name, $this->parameters);
	}

	/**
	 * @param String $name
	 * @return String
	 */
	public function getParameter($name) {
		return $this->hasParameter($name) ? $this->parameters[$name] : null;
	}

	/**
	 * @return array
	 */
	public function getParameterNames() {
		return array_keys($this->parameters);
	}

	/**
	 * @param String $name
	 * @return String
	 */
	public function getHeader($name) {
		$name = strtoupper($name);
		return array_key_exists($name, $this->headers) ? $this->headers[$name] : false;
	}

	/**
	 * @return array
	 */
	public function getHeaderNames() {
		return array_keys($this->headers);
	}

	/**
	 * @param array $params
	 */
	public function addParameters(array $params) {
		$this->parameters = array_merge($this->parameters, $params);
	}

	/**
	 * @return bool
	 */
	public function getAjax() {
		return $this->ajax;
	}
}