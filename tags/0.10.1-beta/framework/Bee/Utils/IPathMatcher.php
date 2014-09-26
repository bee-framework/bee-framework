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

/**
 * Enter description here...
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
interface IPathMatcher {

	/**
	 * Does the given <code>path</code> represent a pattern that can be matched
	 * by an implementation of this interface?
	 * <p>If the return value is <code>false</code>, then the {@link #match}
	 * method does not have to be used because direct equality comparisons
	 * on the static path Strings will lead to the same result.
	 *
	 * @param String $path the path String to check
	 * @return boolean <code>true</code> if the given <code>path</code> represents a pattern
	 */
	function isPattern($path);

	/**
	 * Match the given <code>path</code> against the given <code>pattern</code>,
	 * according to this PathMatcher's matching strategy.
	 *
	 * @param String $pattern the pattern to match against
	 * @param String $path the path String to test
	 * @return boolean <code>true</code> if the supplied <code>path</code> matched,
	 * <code>false</code> if it didn't
	 */
	function match($pattern, $path);
	
	/**
	 * Match the given <code>path</code> against the corresponding part of the given
	 * <code>pattern</code>, according to this PathMatcher's matching strategy.
	 * <p>Determines whether the pattern at least matches as far as the given base
	 * path goes, assuming that a full path may then match as well.
	 *
	 * @param String $pattern the pattern to match against
	 * @param String $path the path String to test
	 * @return boolean <code>true</code> if the supplied <code>path</code> matched,
	 * <code>false</code> if it didn't
	 */
	function matchStart($pattern, $path);

	/**
	 * Given a pattern and a full path, determine the pattern-mapped part.
	 * <p>This method is supposed to find out which part of the path is matched
	 * dynamically through an actual pattern, that is, it strips off a statically
	 * defined leading path from the given full path, returning only the actually
	 * pattern-matched part of the path.
	 * <p>For example: For "myroot/*.html" as pattern and "myroot/myfile.html"
	 * as full path, this method should return "myfile.html". The detailed
	 * determination rules are specified to this PathMatcher's matching strategy.
	 * <p>A simple implementation may return the given full path as-is in case
	 * of an actual pattern, and the empty String in case of the pattern not
	 * containing any dynamic parts (i.e. the <code>pattern</code> parameter being
	 * a static path that wouldn't qualify as an actual {@link #isPattern pattern}).
	 * A sophisticated implementation will differentiate between the static parts
	 * and the dynamic parts of the given path pattern.
	 *
	 * @param String $pattern the path pattern
	 * @param String $path the full path to introspect
	 * @return String the pattern-mapped part of the given <code>path</code>
	 * (never <code>null</code>)
	 */
	function extractPathWithinPattern($pattern, $path);
}
