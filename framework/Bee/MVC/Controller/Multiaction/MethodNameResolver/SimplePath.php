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
 * A very simple method name resolver implementation that uses the last fragment of the path info as the method name.
 *
 * @see Bee_MVC_Controller_MultiAction
 * @see Bee_MVC_Controller_Multiaction_IMethodNameResolver
 * @see Bee_MVC_Controller_Multiaction_IMethodNameResolver
 * 
 * @author Michael Plomer <michael.plomer@iter8.de>
 * @author Benjamin Hartmann
 */
class Bee_MVC_Controller_Multiaction_MethodNameResolver_SimplePath extends Bee_MVC_Controller_Multiaction_MethodNameResolver_Abstract {
	
	public function getHandlerMethodName(Bee_MVC_IHttpRequest $request) {
		$pathInfo = $request->getPathInfo();
		if(Bee_Utils_Strings::hasText($pathInfo)) {
			$parts = explode('/', $pathInfo);
			if(count($parts) > 0) {
				$methodName = $parts[count($parts) - 1];
			}
		}
		return $methodName;
	}
}
?>