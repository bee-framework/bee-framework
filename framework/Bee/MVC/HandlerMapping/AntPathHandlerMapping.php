<?php
namespace Bee\MVC\HandlerMapping;
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
use Bee\MVC\IHttpRequest;
use Bee\Utils\AntPathMatcher;
use Bee\Utils\IPathMatcher;

/**
 * Enter description here...
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class AntPathHandlerMapping extends AbstractHandlerMapping {
	
	/**
	 * Enter description here...
	 *
	 * @var array
	 */
	private $handlerMappings;

	/**
	 * @var IPathMatcher
	 */
	private $pathMatcher;

	public function getHandlerMappings() {
		return $this->handlerMappings;
	}
	
	public function setHandlerMappings($handlerMappings) {
		$this->handlerMappings = $handlerMappings;
	}

	/**
	 * @return IPathMatcher
	 */
	public function getPathMatcher() {
		return $this->pathMatcher;
	}

	/**
	 * @param IPathMatcher $pathMatcher
	 */
	public function setPathMatcher(IPathMatcher $pathMatcher) {
		$this->pathMatcher = $pathMatcher;
	}

	/**
	 * @param IHttpRequest $request
	 * @return mixed
	 */
	protected function getControllerBeanName(IHttpRequest $request) {
		$pathInfo = $request->getPathInfo();
		return $this->getElementByMatchingArrayKey($pathInfo, $this->handlerMappings, $this->getDefaultControllerBeanName());
	}

	/**
	 * @param string $path
	 * @param array $array
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function getElementByMatchingArrayKey($path, array $array = null, $defaultValue = null) {
		$result = $defaultValue;
		if(is_array($array)) {
			if (array_key_exists($path, $array)) {
				// shortcut for direct path matches
				$result = $array[$path];
			} else {
				$matcher = is_null($this->pathMatcher) ? new AntPathMatcher() : $this->pathMatcher;
				foreach($array as $mapping => $element) {
					if($matcher->match($mapping, $path)) {
	//				if(($matcher->isPattern($mapping) && $matcher->match($mapping, $pathInfo)) || Strings::startsWith($pathInfo, $mapping)) {
						$result = $element;
						break;
					}
				}
			}
		}
		return $result;
	}
}