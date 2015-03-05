<?php
function exception_error_handler($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}
set_error_handler("exception_error_handler", E_RECOVERABLE_ERROR);

require_once __DIR__.'/../vendor/autoload.php';

Logger::configure('conf/log4php.xml');

Bee\Cache\Manager::init();

//Bee\Framework::setProductionMode(true);
Bee\Framework::dispatchRequestUsingXmlContext(__DIR__.'/conf/context-newinterceptors.xml');