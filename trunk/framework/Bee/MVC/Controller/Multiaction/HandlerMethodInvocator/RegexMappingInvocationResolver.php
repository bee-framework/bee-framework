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
use Bee\Utils\AntPathToRegexTransformer;
use Bee\Utils\ITypeDefinitions;
use Bee_MVC_IHttpRequest;
use ReflectionClass;
use ReflectionMethod;

/**
 * Class RegexMappingInvocator
 * @package Bee\MVC\Controller\Multiaction\HandlerMethodInvocator
 */
class RegexMappingInvocationResolver implements IInvocationResolver {

	/**
	 * @var array
	 */
	private $mappedMethods = array();

	/**
	 * @param array|\ReflectionMethod[] $mapping
	 */
	public function __construct(array $mapping) {
		foreach($mapping as $antPathPattern => $method) {
			$paramTypes = array();
			$nameToPos = array();
			$fixedParamPos = array();
			foreach($method->getParameters() as $param) {
				$typeName = $param->getClass();
				$typeName = $typeName instanceof ReflectionClass ? $typeName->getName() : null;
				if(!\Bee_Utils_Strings::hasText($typeName)) {
					// todo: derive primitive type names from @var annotations
					$typeName = ITypeDefinitions::STRING;
				} else if($typeName == 'Bee_MVC_IHttpRequest') {
					$fixedParamPos[$param->getPosition()] = true;
				}
				$paramTypes[$param->getName()] = $typeName;
				$paramTypes[$param->getPosition()] = $typeName;
				$nameToPos[$param->getName()] = $param->getPosition();
			}
			$positionMap = array();
			$regex = AntPathToRegexTransformer::getRegexForParametrizedPattern($antPathPattern, $paramTypes, $positionMap);
			$positionMap = array_map(function ($value) use ($nameToPos) {
				return array_key_exists($value, $nameToPos) ? $nameToPos[$value] : $value;
			}, $positionMap);
			// positionMap should only contain numeric values, mapping from match position to parameter position
			$positionMap = array_flip($positionMap);
			// now from parameter pos to match pos

			if(!array_key_exists($regex, $this->mappedMethods)) {
				// todo: object?
				$this->mappedMethods[$regex] = array(
					'method' => $method,
					'positionMap' => $positionMap,
					'requestParamPos' => $fixedParamPos,
					'typeMap' => $paramTypes
				);
			}
		}
	}

	/**
	 * @param Bee_MVC_IHttpRequest $request
	 * @return array
	 */
	public function getInvocationDefinition(Bee_MVC_IHttpRequest $request) {
		$pathInfo = $request->getPathInfo();
		foreach($this->mappedMethods as $regex => $invocInfo) {
			$matches = array();
			if(preg_match($regex, $pathInfo, $matches) === 1) {
				return array_merge($invocInfo, array('paramValues' => $matches));
			}
		}
		// todo: maybe throw exception? how do we determine if method could not be found?
		return null;
	}
}