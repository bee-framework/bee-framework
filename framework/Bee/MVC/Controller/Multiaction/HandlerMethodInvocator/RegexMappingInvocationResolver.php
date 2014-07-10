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
use Bee_MVC_IHttpRequest;
use Logger;
use ReflectionMethod;

/**
 * Class RegexMappingInvocator
 * @package Bee\MVC\Controller\Multiaction\HandlerMethodInvocator
 */
class RegexMappingInvocationResolver implements IInvocationResolver {

	const IGNORE_WILDCARD_TRAILING_PATTERN = '\\.[^/]*';

	/**
	 * @var Logger
	 */
	protected static $log;

	/**
	 * @return Logger
	 */
	protected static function getLog() {
		if (!self::$log) {
			self::$log = Logger::getLogger(get_called_class());
		}
		return self::$log;
	}

	/**
	 * @var HandlerMethodMetadata[]
	 */
	private static $methodMetadataMap;

	/**
	 * @var MethodInvocation[]
	 */
	private $mappedMethods = array();

	/**
	 * @param array|ReflectionMethod[] $mapping
	 */
	public function __construct(array $mapping) {
		foreach ($mapping as $antPathPattern => $method) {
			$methodMeta = self::getCachedMethodMetadata($method);

			$urlParameterPositions = array();
			$regex = AntPathToRegexTransformer::getRegexForParametrizedPattern($antPathPattern, $methodMeta->getTypeMap(), $urlParameterPositions);
			$nameToPos = $methodMeta->getParameterPositions();
			$urlParameterPositions = array_map(function ($value) use ($nameToPos) {
				return array_key_exists($value, $nameToPos) ? $nameToPos[$value] : $value;
			}, $urlParameterPositions);
			// positionMap should only contain numeric values, mapping from match position to parameter position
			$urlParameterPositions = array_flip($urlParameterPositions);
			// now from parameter pos to match pos

			$this->mappedMethods[$regex] = new MethodInvocation($methodMeta, $antPathPattern, $urlParameterPositions);
		}
	}

	/**
	 * @param Bee_MVC_IHttpRequest $request
	 * @return MethodInvocation
	 */
	public function getInvocationDefinition(Bee_MVC_IHttpRequest $request) {
		$pathInfo = $request->getPathInfo();
		$matchingPatterns = array();
		foreach ($this->mappedMethods as $regex => $invocInfo) {
			$matches = array();
			if (preg_match($regex, $pathInfo, $matches) === 1) {
				$invocInfo->setParamValues($matches);
//				return $invocInfo;
				$matchingPatterns[$regex] = $invocInfo;
			}
		}
		// sort matching patterns
		if (self::getLog()->isDebugEnabled()) {
			self::getLog()->debug('Found ' . count($matchingPatterns) . ' matching patterns for pathInfo "' . $pathInfo . '", trying to determine most specific match');
		}

		uksort($matchingPatterns, function ($patternA, $patternB) use ($matchingPatterns, $pathInfo) {
			/** @var MethodInvocation[] $matchingPatterns */
			$matchA = $matchingPatterns[$patternA];
			$matchB = $matchingPatterns[$patternB];

			$emptyMatches = 0;
			$litLength = function ($carry, $item) use (&$emptyMatches) {
				if ($carry === false) {
					return $item;
				}
				if($item) {
					if(substr($item, -1) == '/') {
						$item = substr($item, 0, -1);
					}
					return preg_replace('#/'.preg_quote($item).'#', '', $carry, 1);
				}
				$emptyMatches++;
				return $carry;
			};
			$litA = array_reduce($matchA->getParamValues(), $litLength, false);
			$litA = preg_replace('#//#', '/', $litA);

			$emptyMatches *= -1;

			$litB = array_reduce($matchB->getParamValues(), $litLength, false);
			$litB = preg_replace('#//#', '/', $litB);
			$litCountA = substr_count($litA, '/');
			$litCountB = substr_count($litB, '/');

			if ($litCountA != $litCountB) {
				return $litCountB - $litCountA;
			}

			$resA = array_diff_key($matchA->getParamValues(), array_flip($matchA->getUrlParameterPositions()), array(0 => true));
			$resB = array_diff_key($matchB->getParamValues(), array_flip($matchB->getUrlParameterPositions()), array(0 => true));

			$counter = function ($carry, $item) {
				if (strlen($item) == 0) {
					return $carry;
				}
				$count = substr_count($item, '/');
				return $carry + $count + (substr($item, -1) == '/' ? 0 : 1);
			};
			$countA = array_reduce($resA, $counter, 0);
			$countB = array_reduce($resB, $counter, 0);

			if ($countA != $countB) {
				return $countA - $countB;
			}
			return -$emptyMatches;
		});

		if (self::getLog()->isDebugEnabled()) {
			$matchingAntPaths = array_map(function (MethodInvocation $item) {
				return $item->getAntPathPattern();
			}, $matchingPatterns);
			self::getLog()->debug('Sorted patterns for pathInfo "' . $pathInfo . '" are ' . "\n" . implode("\n", $matchingAntPaths));
		}

		if (count($matchingPatterns) > 0) {
			return array_shift($matchingPatterns);
		}

		return null;
	}

	public static function getCachedMethodMetadata(ReflectionMethod $method) {
		$methodFullName = $method->getDeclaringClass()->getName() . '::' . $method->getName();
		if (!array_key_exists($methodFullName, self::$methodMetadataMap)) {
			self::$methodMetadataMap[$methodFullName] = new HandlerMethodMetadata($method);
		}
		return self::$methodMetadataMap[$methodFullName];
	}

}