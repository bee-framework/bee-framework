<?php

namespace Bee\Persistence\Doctrine2\BeanInjection;


/**
 * Class Inject
 * @package Bee\Persistence\Doctrine2\BeanInjection
 *
 * @Annotation
 * @Target({"METHOD","PROPERTY"})
 */
class Inject {
	/**
	 * @var string
	 */
	public $beanName;
} 