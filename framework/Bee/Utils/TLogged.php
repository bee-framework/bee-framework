<?php
/*
 * Copyright 2008-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Bee\Utils;

use Bee\Framework;
use Logger;

/**
 * Trait TLogged
 * Logging trait using a per-instance logger. Use this for classes that have only few instances, or in class hierarchies.
 *
 * PROS:
 *  - each instance (and therefore each concrete class) has its own logger, as opposed to different subclasses logging
 *    into one superclass logger.
 * CONS:
 *  - not resource efficient. Every instance of a class performs logger instantiation (or at least lookup)
 *  - absent a "transient" keyword in PHP, the Logger is serialized together with the rest of the class, which is in
 *    general NOT desired
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