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
 * A method name resolver that uses a mapping from ant-style path definitions to handler method names. The path definitions are parsed
 * by <code>Bee_Utils_AntPathMatcher</code> and can contain wildcards (*, ?).
 *
 * @see Bee_MVC_Controller_MultiAction
 * @see Bee_MVC_Controller_Multiaction_IMethodNameResolver
 * @see Bee_Utils_AntPathMatcher
 * 
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class Bee_MVC_Controller_Multiaction_MethodNameResolver_AntPath extends Bee_MVC_Controller_Multiaction_MethodNameResolver_Abstract {

	/**
	 * Enter description here...
	 *
	 * @var array
	 */
	private $methodMappings;
	
	
	/**
	 * Enter description here...
	 *
	 * @param array $methodMapping
	 * @return void
	 */
	public function setMethodMappings(array $methodMappings) {
		$this->methodMappings = $methodMappings;
	}
	
	
	/**
	 * Enter description here...
	 *
	 * @return array
	 */
	public function getMethodMappings() {
		return $this->methodMappings;
	}	
	
	
	public function getHandlerMethodName(Bee_MVC_IHttpRequest $request) {
		$matcher = new Bee_Utils_AntPathMatcher();
		$pathInfo = $request->getPathInfo();

		$handlerMethodName = null;

		if(array_key_exists($pathInfo, $this->methodMappings)) {
			// shortcut for direct path matches
			$handlerMethodName = $this->methodMappings[$pathInfo];
		} else {
			foreach($this->methodMappings as $mapping => $method) {
				if($matcher->match($mapping, $pathInfo)) {
					$handlerMethodName = $method;
					break;
				}
			}
		}
		
		return $handlerMethodName;
	}
}
