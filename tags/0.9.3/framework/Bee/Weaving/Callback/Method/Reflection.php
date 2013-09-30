<?php
/*
 * Copyright 2008-2010 the original author or authors.
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

/**
 * User: mp
 * Date: 05.07.11
 * Time: 04:14
 */
 
class Bee_Weaving_Callback_Method_Reflection implements Bee_Weaving_Callback_IMethod {

    private $instance;

	private $className;

	private $methodName;

    /**
     * @var ReflectionMethod
     */
    private $method;

    public function __construct($instance, $className, $methodName) {
        $this->instance = $instance;
		$this->className = $className;
		$this->methodName = $methodName;
		$this->initializeMethod();
    }

    public function invoke(array $args = null) {
        return $this->method->invokeArgs($this->instance, $args);
    }

	public function invokeOnInstance($instance, array $args = null) {
		return $this->method->invokeArgs($instance, $args);
	}

	private function initializeMethod() {
		$this->method = new ReflectionMethod($this->className, $this->methodName);
		if(method_exists($this->className, 'setAccessible')) {
			$this->method->setAccessible(true);
		}
	}

	public function __sleep() {
		return array( 'instance', 'className', 'methodName' );
	}

	public function __wakeup() {
		$this->initializeMethod();
	}
}
