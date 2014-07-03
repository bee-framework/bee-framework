<?php
namespace Bee\MVC\Controller\Multiaction\MethodNameResolver;
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
use Bee\MVC\Controller\Multiaction\IMethodNameResolver;
use Bee\MVC\Controller\MultiActionController;

/**
 * Abstract base class for method name resolvers. Carries a reference to the controller that uses it.
 * 
 * <b>ATTENTION:</b> if a method name resolver is implemented statefully (which is the case if its functionality depends on the owning
 * controller), care must be taken that the method name resolver is either not injected into multiple controllers or has prototype scope! 
 * 
 * @see Bee\MVC\Controller\MultiAction
 * @see Bee\MVC\Controller\Multiaction\IMethodNameResolver
 * 
 * @author Michael Plomer <michael.plomer@iter8.de> 
 */
abstract class AbstractMethodNameResolver implements IMethodNameResolver {

	/**
	 * Enter description here...
	 *
	 * @var MultiActionController
	 */
	private $controller;
		
	public function setController(MultiActionController $controller) {
		$this->controller = $controller;
	}

	/**
	 * Enter description here...
	 * 
	 * @return MultiActionController
	 */
	public function getController() {
		return $this->controller;
	}	
}