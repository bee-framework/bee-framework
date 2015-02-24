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
use Bee\Beans\PropertyEditor\PropertyEditorRegistry;
use Bee\MVC\IHttpRequest;
use Bee\Utils\AntPathToRegexTransformer;
use Bee\Utils\TLogged;
use Logger;
use ReflectionMethod;

/**
 * Class RegexMappingInvocator
 * @package Bee\MVC\Controller\Multiaction\HandlerMethodInvocator
 */
class RegexMappingInvocationResolver implements IInvocationResolver {
    use TLogged;

	const IGNORE_WILDCARD_TRAILING_PATTERN = '\\.[^/]*';

	/**
	 * @var HandlerMethodMetadata[]
	 */
	private static $methodMetadataMap = array();

	/**
	 * @var MethodInvocation[]
	 */
	private $mappedMethods = array();

	/**
	 * @param array|ReflectionMethod[] $mapping
	 * @param PropertyEditorRegistry $propertyEditorRegistry
	 */
	public function __construct(array $mapping, PropertyEditorRegistry $propertyEditorRegistry) {
		foreach ($mapping as $antPathPattern => $method) {
			$methodMeta = self::getCachedMethodMetadata($method, $propertyEditorRegistry);

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
	 * @param IHttpRequest $request
	 * @return MethodInvocation
	 */
	public function getInvocationDefinition(IHttpRequest $request) {
		$pathInfo = $request->getPathInfo();
		/** @var MethodInvocation[] $matchingPatterns */
		$matchingPatterns = array();
		foreach ($this->mappedMethods as $regex => $invocInfo) {
			if (preg_match($regex, $pathInfo, $matches) === 1) {
				$invocInfo->setParamValues($matches);
				$matchingPatterns[$regex] = $invocInfo;
			}
		}
		// sort matching patterns
		if ($this->getLog()->isDebugEnabled()) {
			$this->getLog()->debug('Found ' . count($matchingPatterns) . ' matching patterns for pathInfo "' . $pathInfo . '", trying to determine most specific match');
		}

		uksort($matchingPatterns, function ($patternA, $patternB) use ($matchingPatterns, $pathInfo) {
			$emptyMatches = 0;
			$litLength = function ($carry, $item) use (&$emptyMatches) {
				if ($carry === false) {
					return $item;
				}
				if ($item) {
					if (substr($item, -1) == '/') {
						$item = substr($item, 0, -1);
					}
					return preg_replace('#/' . preg_quote($item) . '#', '', $carry, 1);
				}
				$emptyMatches++;
				return $carry;
			};

			$matchA = $matchingPatterns[$patternA];
			$litA = array_reduce($matchA->getParamValues(), $litLength, false);
			$litA = preg_replace('#//#', '/', $litA);
			$litCountA = substr_count($litA, '/');

			$emptyMatches *= -1;

			$matchB = $matchingPatterns[$patternB];
			$litB = array_reduce($matchB->getParamValues(), $litLength, false);
			$litB = preg_replace('#//#', '/', $litB);
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
			if($emptyMatches != 0) {
				return -$emptyMatches;
			}
			return strlen($matchA->getAntPathPattern()) - strlen($matchB->getAntPathPattern());
		});

		if ($this->getLog()->isDebugEnabled()) {
			$matchingAntPaths = array_map(function (MethodInvocation $item) {
				return $item->getAntPathPattern();
			}, $matchingPatterns);
			$this->getLog()->debug('Sorted patterns for pathInfo "' . $pathInfo . '" are ' . "\n" . implode("\n", $matchingAntPaths));
		}

		if (count($matchingPatterns) > 0) {
			return array_shift($matchingPatterns);
		}

		return null;
	}

	public static function getCachedMethodMetadata(ReflectionMethod $method, PropertyEditorRegistry $propertyEditorRegistry) {
		$methodFullName = $method->getDeclaringClass()->getName() . '::' . $method->getName();
		if (!array_key_exists($methodFullName, self::$methodMetadataMap)) {
			self::$methodMetadataMap[$methodFullName] = new HandlerMethodMetadata($method, $propertyEditorRegistry);
		}
		return self::$methodMetadataMap[$methodFullName];
	}
}