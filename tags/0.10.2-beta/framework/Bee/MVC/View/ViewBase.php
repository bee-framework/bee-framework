<?php
namespace Bee\MVC\View;

use Bee\MVC\IHttpStatusCodes;
use Bee\MVC\IView;

/**
 * Class ViewBase
 * @package Bee\MVC\View
 */
abstract class ViewBase implements IView, IHttpStatusCodes {

	/**
	 * @var array
	 */
	private $staticAttributes = array();

	/**
	 * @Return void
	 * @Param name String
	 * @Param object Object
	 */
	public final function addStaticAttribute($name, $object) {
		$this->staticAttributes[$name] = $object;
	}

	public function setStaticAttributes(array $staticAttributes) {
		$this->staticAttributes = array_merge($this->staticAttributes, $staticAttributes);
	}

	public function getStaticAttributes() {
		return $this->staticAttributes;
	}
} 