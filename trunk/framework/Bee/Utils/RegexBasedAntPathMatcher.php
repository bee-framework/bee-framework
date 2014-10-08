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
use Exception;

/**
 * Class RegexBasedAntPathMatcher
 * @package Bee\Utils
 */
class RegexBasedAntPathMatcher implements IPathMatcher {

	/**
	 * @param String $path
	 * @return bool
	 */
	public function isPattern($path) {
		return strpos($path, '*') !== false || strpos($path, '?') !== false;
	}

	/**
	 * Match the given <code>path</code> against the given <code>pattern</code>,
	 * according to this PathMatcher's matching strategy.
	 *
	 * @param String $pattern the pattern to match against
	 * @param String $path the path String to test
	 * @return boolean <code>true</code> if the supplied <code>path</code> matched,
	 * <code>false</code> if it didn't
	 */
	function match($pattern, $path) {
		$regex = AntPathToRegexTransformer::getRegexForSimplePattern($pattern);
		return preg_match($regex, $path);
	}

	/**
	 * @param String $pattern
	 * @param String $path
	 * @return bool|void
	 * @throws Exception
	 */
	function matchStart($pattern, $path) {
		throw new Exception('Not implemented');
	}

	/**
	 * @param String $pattern
	 * @param String $path
	 * @return String|void
	 * @throws Exception
	 */
	function extractPathWithinPattern($pattern, $path) {
		throw new Exception('Not implemented');
	}
}