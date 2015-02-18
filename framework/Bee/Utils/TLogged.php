<?php
namespace Bee\Utils;

use Bee\Framework;
use Logger;

/**
 * Class TLogged
 * @package Bee\Utils
 */
trait TLogged {

    /**
     * @var Logger
     */
    protected $log;

    /**
     * @return Logger
     */
    public function getLog() {
        if (!$this->log) {
            $this->log = Framework::getLoggerForClass(get_class($this));
        }
        return $this->log;
    }
}