<?php
namespace Bee\Context\Config;

use Bee\IContext;

/**
 * Class TContextAware
 * @package Bee\Context\Config
 */
trait TContextAware {

    /**
     * @var IContext
     */
    protected $context;

    /**
     * @param IContext $context
     */
    public function setBeeContext(IContext $context) {
        $this->context = $context;
    }

}