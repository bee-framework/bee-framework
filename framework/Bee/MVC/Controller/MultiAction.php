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
 * The multiaction controller handles requests by delegating to method calls on a delegate object. The methods to be used are
 * determined by the method name resolver.
 * 
 * A handler method must take at least one argument, type-hinted to Bee_MVC_IHttpRequest, and return a Bee_MVC_ModelAndView.
 * 
 * @see Bee_MVC_IHttpRequest
 * @see Bee_MVC_ModelAndView
 * 
 * @author Michael Plomer <michael.plomer@iter8.de>
 * @author Benjamin Hartmann
 */
class Bee_MVC_Controller_MultiAction extends Bee_MVC_Controller_Abstract {
	
	/**
	 * Enter description here...
	 *
	 * @todo: What is the rationale behind having the defaultMethodName in the Controller rather than in the MethodNameResolver (Spring places it in the latter)?
	 * @var String
	 */
	private $defaultMethodName; 
	


	/**
	 * Enter description here...
	 *
	 * @var object
	 */
	private $delegate;
	
	
	
	/**
	 * Enter description here...
	 *
	 * @var Bee_MVC_Controller_Multiaction_IMethodNameResolver
	 */
	private $methodNameResolver;
	
	protected function init() {
		Bee_Utils_Assert::notNull($this->delegate, '\'delegate\' property is required in Bee_MVC_Controller_MultiAction');
		Bee_Utils_Assert::notNull($this->methodNameResolver, '\'methodNameResolver\' property is required in Bee_MVC_Controller_MultiAction');
	}
	
	
	
	/**
	 * Enter description here...
	 *
	 * @return Bee_MVC_ModelAndView
	 */
	protected function handleRequestInternally(Bee_MVC_IHttpRequest $request) {
		
		$methodName = $this->methodNameResolver->getHandlerMethodName($request);
    	
		if($methodName instanceof ReflectionMethod) {

			$method = $methodName;

		} else {
			
			if(!Bee_Utils_Strings::hasText($methodName)) {
				$methodName = $this->getDefaultMethodName();
			}
				
			// @todo: this might pose a security risk. introduce a set of allowed method names			
			$method = new ReflectionMethod($this->delegate, $methodName);
			if(!$this->isHandlerMethod($method)) {
				throw new Exception('No request handling method with name '.$methodName.' in class ['.Bee_Utils_Types::getType($this->delegate).']');
			}
		}
		$mav = $method->invokeArgs($this->delegate, array($request));
		return $mav;
	}
	
	
	public final function isHandlerMethod(ReflectionMethod $method) {
		if(Bee_Utils_Reflection::isCallableRegularMethod($method)) {
			$parameters = $method->getParameters();
			$paramCount = count($parameters); 
			if($paramCount < 1) {
				return false;
			}
			// first param must be of type Bee_MVC_IHttpRequest
			$class1 = $parameters[0]->getClass();
			if(is_null($class1) || $class1->getName() !== 'Bee_MVC_IHttpRequest') {
				return false; 
			}
			// @todo: 2nd parameter can be required for exception handlers or if command object
			// all remaining params must be optional
			for($i = 1; $i < $paramCount; $i++) {
				$param = $parameters[$i];
				if(!$param->isOptional()) {
					return false;
				}
			}
			return true;
		}
		return false; 
	}

	
	
	/**
	 * Enter description here...
	 *
	 * @param object $delegate
	 * @return void
	 */
	public final function setDelegate($delegate) {
		$this->delegate = $delegate;	
	}

	
	
	/**
	 * Enter description here...
	 *
	 * @return object
	 */
	public function getDelegate() {
		return $this->delegate;
	}
	
	
	
	/**
	 * Enter description here...
	 *
	 * @param Bee_MVC_Controller_Multiaction_IMethodNameResolver $methodNameResolver
	 * @return void
	 */
	public final function setMethodNameResolver($methodNameResolver) {
		$this->methodNameResolver = $methodNameResolver;
		if(!is_null($this->methodNameResolver)) {
			$this->methodNameResolver->setController($this);
		}
	}
	
	
	
	/**
	 * Enter description here...
	 *
	 * @return Bee_MVC_Controller_Multiaction_IMethodNameResolver
	 */
	public function getMethodNameResolver() {
		return $this->methodNameResolver;
	}
	
	
	
	/**
	 * Enter description here...
	 *
	 * @return String
	 */
	public function getDefaultMethodName() {
		return $this->defaultMethodName;
	}
	
	
	
	/**
	 * Enter description here...
	 *
	 * @param String $defaultMethodName
	 * @return void
	 */
	public function setDefaultMethodName($defaultMethodName) {
		$this->defaultMethodName = $defaultMethodName;
	}
}

?>