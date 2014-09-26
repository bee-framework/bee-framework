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
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 *
 * @deprecated
 */
class Bee_MVC_HandlerMapping_SimpleBeanNameUrl extends Bee_MVC_HandlerMapping_AbstractPathInfoBased {
	
	protected function getControllerBeanName(Bee_MVC_IHttpRequest $request) {
		$pathInfo = $request->getPathInfo();
		
		$parts = explode('/', $pathInfo);
		
		if(Bee_Utils_Strings::hasText($parts[1])) {
			$controllerBeanName = '/'.$parts[1];
		} else {
			$controllerBeanName = $this->getDefaultControllerBeanName();
		}
		
		return $controllerBeanName;
	}
	
}
?>