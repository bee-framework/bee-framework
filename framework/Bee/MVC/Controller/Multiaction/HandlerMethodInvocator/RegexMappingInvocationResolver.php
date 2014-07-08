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
use Addendum\ReflectionAnnotatedMethod;
use Bee\Utils\AntPathToRegexTransformer;
use Bee_MVC_IHttpRequest;
use ReflectionMethod;

/**
 * Class RegexMappingInvocator
 * @package Bee\MVC\Controller\Multiaction\HandlerMethodInvocator
 */
class RegexMappingInvocationResolver implements IInvocationResolver {

	/**
	 * @var HandlerMethodMetadata[]
	 */
	private static $methodMetadataMap = array();

	/**
	 * @var MethodInvocation[]
	 */
	private $mappedMethods = array();

	/**
	 * @param array|ReflectionAnnotatedMethod[] $mapping
	 */
	public function __construct(array $mapping) {
		foreach ($mapping as $antPathPattern => $method) {
			$methodMeta = new HandlerMethodMetadata($method);

			$urlParameterPositions = array();
			$regex = AntPathToRegexTransformer::getRegexForParametrizedPattern($antPathPattern, $methodMeta->getTypeMap(), $urlParameterPositions);
			$nameToPos = $methodMeta->getParameterPositions();
			$urlParameterPositions = array_map(function ($value) use ($nameToPos) {
				return array_key_exists($value, $nameToPos) ? $nameToPos[$value] : $value;
			}, $urlParameterPositions);
			// positionMap should only contain numeric values, mapping from match position to parameter position
			$urlParameterPositions = array_flip($urlParameterPositions);
			// now from parameter pos to match pos

			if (!array_key_exists($regex, $this->mappedMethods)) {
				$this->mappedMethods[$regex] = new MethodInvocation($methodMeta, $urlParameterPositions);
			}
		}
	}

	/**
	 * @param Bee_MVC_IHttpRequest $request
	 * @return MethodInvocation
	 */
	public function getInvocationDefinition(Bee_MVC_IHttpRequest $request) {
		$pathInfo = $request->getPathInfo();
		foreach ($this->mappedMethods as $regex => $invocInfo) {
			$matches = array();
			if (preg_match($regex, $pathInfo, $matches) === 1) {
				$invocInfo->setParamValues($matches);
				return $invocInfo;
			}
		}
		return null;
	}

	public static function getCachedMethodMetadata(ReflectionMethod $method) {
		$methodFullName = $method->getDeclaringClass()->getName() .'::' . $method->getName();
		if(!array_key_exists($methodFullName, self::$methodMetadataMap)) {
			self::$methodMetadataMap[$methodFullName] = new HandlerMethodMetadata($method);
		}
		return self::$methodMetadataMap[$methodFullName];
	}
}