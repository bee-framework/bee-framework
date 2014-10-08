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
use Bee\Context\AbstractContext;

/**
 * User: mp
 * Date: 05.07.11
 * Time: 15:38
 */
 
class Bee_AOP_Scope_ScopedProxyMethodInterceptor implements Bee_Weaving_Callback_IMethodInterceptor {

	/**
	 * The name of the target bean
	 * @var string
	 */
	private $targetBeanName;

	private $contextIdentifier;

	private $targetObject;

	public function __construct($targetBeanName, $contextIdentifier) {
		$this->targetBeanName = $targetBeanName;
		$this->contextIdentifier = $contextIdentifier;
	}

	public function intercept($object, $methodName, Bee_Weaving_Callback_IMethod $method, array $args = null) {
		$this->resolveTargetObjectIfNecessary();
		return $method->invokeOnInstance($this->targetObject, $args);
	}

	private function resolveTargetObjectIfNecessary() {
		if($this->targetObject == null) {
			$context = AbstractContext::getRegisteredContext($this->contextIdentifier);
			$this->targetObject = $context->getBean($this->targetBeanName);
		}
	}

	public function __sleep() {
		$this->targetObject = null;
		return array('contextIdentifier', 'targetBeanName');
	}
}
