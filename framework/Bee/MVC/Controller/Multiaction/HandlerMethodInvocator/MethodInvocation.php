<?php
namespace Bee\MVC\Controller\Multiaction\HandlerMethodInvocator;
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

/**
 * Class MethodInvocation
 * @package Bee\MVC\Controller\Multiaction\HandlerMethodInvocator
 */
class MethodInvocation {

	/**
	 * @var HandlerMethodMetadata
	 */
	private $methodMeta;

	/**
	 * @var string
	 */
	private $antPathPattern;

	/**
	 * @var array
	 */
	private $paramValues;

	/**
	 * @var array
	 */
	private $urlParameterPositions;

	function __construct(HandlerMethodMetadata $methodMetadata, $antPathPattern = false, array $urlParameterPositions = array()) {
		$this->methodMeta = $methodMetadata;
		$this->antPathPattern = $antPathPattern;
		$this->urlParameterPositions = $urlParameterPositions;
	}

	/**
	 * @return HandlerMethodMetadata
	 */
	public function getMethodMeta() {
		return $this->methodMeta;
	}

	/**
	 * @return array
	 */
	public function getUrlParameterPositions() {
		return $this->urlParameterPositions;
	}

	/**
	 * @return array
	 */
	public function getParamValues() {
		return $this->paramValues;
	}

	/**
	 * @param array $paramValues
	 */
	public function setParamValues(array $paramValues) {
		$this->paramValues = $paramValues;
	}

	/**
	 * @param $pos
	 * @return mixed
	 */
	public function getParamValue($pos) {
		return $this->paramValues[$pos];
	}

	/**
	 * @return string
	 */
	public function getAntPathPattern() {
		return $this->antPathPattern;
	}
}