<?php
namespace Bee\Utils;
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
use ReflectionClass;

/**
 * Created by IntelliJ IDEA.
 * User: mp
 * Date: Feb 18, 2010
 * Time: 4:30:57 AM
 * To change this template use File | Settings | File Templates.
 */

class Constants {

    /**
     * @var ReflectionClass
     */
    private $reflectedClass;

	/**
	 * @param $className
	 */
	public function __construct($className) {
        $this->reflectedClass = new ReflectionClass($className);
    }

	/**
	 * @param $name
	 * @return mixed
	 */
	public function valueFor($name) {
        return $this->reflectedClass->getConstant($name);
    }

	/**
	 * @param $strVal
	 * @return mixed
	 */
	public static function getValueFromString($strVal) {
		$sepPos = strpos($strVal, '::');
		$inst = new Constants(substr($strVal, 0, $sepPos));
		return $inst->valueFor(substr($strVal, $sepPos + 2));
	}
}