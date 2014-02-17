<?php
namespace Bee\MVC\XmlNamespace;

/**
 * Class Handler
 * @package Bee\MVC\XmlNamespace
 */
class Handler extends \Bee_Context_Xml_Namespace_HandlerSupport {

	function init() {
		$this->registerBeanDefinitionParser('viewresolver', new ViewResolverBeanDefinitionCreator());
	}
}
