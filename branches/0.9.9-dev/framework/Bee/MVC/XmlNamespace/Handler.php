<?php
namespace Bee\MVC\XmlNamespace;
use Bee\Context\Xml\XmlNamespace\HandlerSupport;

/**
 * Class Handler
 * @package Bee\MVC\XmlNamespace
 */
class Handler extends HandlerSupport {

	function init() {
		$this->registerBeanDefinitionParser('viewresolver', new ViewResolverBeanDefinitionCreator());
	}
}
