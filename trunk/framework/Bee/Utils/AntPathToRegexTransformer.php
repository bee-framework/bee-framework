<?php
namespace Bee\Utils;
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
use Bee_Utils_ITypeDefinitions;

/**
 * Class AntPathToRegexTransformer
 * @package Bee\Utils
 */
class AntPathToRegexTransformer {

	private static $SIMPLE_REPLACEMENTS = array(
			'#\.#' => '\\.',
			'#\*#' => '[^/]*',
			'#\[\^/\]\*\[\^/\]\*#' => '.*',
			'#\?#' => '[^/]'
	);

	public static $TYPE_EXPRESSION_MAP = array(
			Bee_Utils_ITypeDefinitions::INT => '\d+',
			Bee_Utils_ITypeDefinitions::INTEGER => '\d+',
			Bee_Utils_ITypeDefinitions::BOOLEAN => '1|0|true|false|on|off|yes|no',
			Bee_Utils_ITypeDefinitions::DOUBLE => '\d+(?:\[.]\d+)?',
			Bee_Utils_ITypeDefinitions::FLOAT => '\d+(?:\[.]\d+)?',
			Bee_Utils_ITypeDefinitions::STRING => '[^/]+'
	);

	const PARAMETER_MATCH = '#{((?:\d+)|(?:[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*))}#';

	/**
	 * @param string $antPathPattern
	 * @return string
	 */
	public static function getRegexForSimplePattern($antPathPattern) {
		return '#^' . preg_replace(array_keys(self::$SIMPLE_REPLACEMENTS), self::$SIMPLE_REPLACEMENTS, $antPathPattern) . '$#';
	}

	/**
	 * @param string $antPathPattern
	 * @param array $parameterTypes
	 * @param array $positionMap
	 * @return string
	 */
	public static function getRegexForParametrizedPattern($antPathPattern, array $parameterTypes, array &$positionMap = array()) {
		$result = self::getRegexForSimplePattern($antPathPattern);
		return preg_replace_callback(self::PARAMETER_MATCH, function($matches) use ($parameterTypes, &$positionMap) {
			$positionMap[] = $matches[1];
			$typeName = $parameterTypes[$matches[1]];
			if(!array_key_exists($typeName, AntPathToRegexTransformer::$TYPE_EXPRESSION_MAP)) {
				$typeName = Bee_Utils_ITypeDefinitions::STRING;
			}
			return '(' . AntPathToRegexTransformer::$TYPE_EXPRESSION_MAP[$typeName] . ')';
		}, $result);
	}

	/**
	 * @param $antPathPattern
	 * @return bool
	 */
	public static function isParametrized($antPathPattern) {
		$matches = array();
		return !!preg_match(self::PARAMETER_MATCH, $antPathPattern, $matches);
	}
}