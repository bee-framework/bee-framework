<?php
namespace Bee\Persistence\Doctrine2\BeanInjection;

use Doctrine\Common\Annotations\Annotation;

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