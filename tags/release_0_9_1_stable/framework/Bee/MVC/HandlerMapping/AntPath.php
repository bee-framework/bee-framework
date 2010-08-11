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
 * Enter description here...
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class Bee_MVC_HandlerMapping_AntPath extends Bee_MVC_HandlerMapping_Abstract {
	
	/**
	 * Enter description here...
	 *
	 * @var array
	 */
	private $handlerMappings;
	
	public function getHandlerMappings() {
		return $this->handlerMappings;
	}
	
	public function setHandlerMappings($handlerMappings) {
		$this->handlerMappings = $handlerMappings;
	}
	
	protected function getControllerBeanName(Bee_MVC_IHttpRequest $request) {
		$matcher = new Bee_Utils_AntPathMatcher();
//		$pathInfo = Bee_Utils_Env::getPathInfo();
		$pathInfo = $request->getPathInfo();
		
		$controllerBeanName = $this->getDefaultControllerBeanName();
		
		if(array_key_exists($pathInfo, $this->handlerMappings)) {
			// shortcut for direct path matches
			$controllerBeanName = $this->handlerMappings[$pathInfo];
		} else {
			foreach($this->handlerMappings as $mapping => $handler) {
				if($matcher->match($mapping, $pathInfo)) {
//				if(($matcher->isPattern($mapping) && $matcher->match($mapping, $pathInfo)) || Bee_Utils_Strings::startsWith($pathInfo, $mapping)) {
					$controllerBeanName = $handler;
					break;
				}
			}
		}
				
		return $controllerBeanName;
	}
}
?>