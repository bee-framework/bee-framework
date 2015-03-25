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
 * Trait TLoggedStatic
 * Logging trait using a static logger. Use this for classes that have lots of instances. Do not use it in class hierarchies
 * above the leaf level.
 *
 * PROS:
 *  - resource efficient, as only one logger exists per class (hierarchy), as opposed to one per instance.
 * CONS:
 *  - not suitable for use in class hierarchies. If a superclass uses the logging trait, the logger name will depend
 *    on which subclass fist used it...
 *
 * todo: do some profiling on the Framework::getLoggerForClass() call. Maybe we can do away with the static type member
 * todo: completely, thus avoiding the cons?
 * @package Bee\Utils
 */
trait TLoggedStatic {

    /**
     * @var Logger
     */
    protected static $log;

    /**
     * @return Logger
     */
    public static function getLog() {
        if (!self::$log) {
            self::$log = Framework::getLoggerForClass(get_called_class());
        }
        return self::$log;
    }
}