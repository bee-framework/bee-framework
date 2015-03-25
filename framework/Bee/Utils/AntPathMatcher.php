<?php
namespace Bee\Utils;
/*
 * Copyright 2008-2015 the original author or authors.
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
class AntPathMatcher implements IPathMatcher {

	/** Default path separator: "/" */
	const DEFAULT_PATH_SEPARATOR = '/';

	/**
	 * Enter description here...
	 *
	 * @var String
	 */
	private $pathSeparator = self::DEFAULT_PATH_SEPARATOR;


	/**
	 * Set the path separator to use for pattern parsing.
	 * Default is "/", as in Ant.
	 */
	public function setPathSeparator($pathSeparator) {
		$this->pathSeparator = (!is_null($pathSeparator) ? $pathSeparator : self::DEFAULT_PATH_SEPARATOR);
	}


	public function isPattern($path) {
		return strpos($path, '*') !== false || strpos($path, '?') !== false;
	}

	public function match($pattern, $path) {
		return $this->doMatch($pattern, $path, true);
	}

	public function matchStart($pattern, $path) {
		return $this->doMatch($pattern, $path, false);
	}

	/**
	 * Actually match the given <code>path</code> against the given <code>pattern</code>.
	 * @param string $pattern the pattern to match against
	 * @param string $path the path to test
	 * @param bool $fullMatch whether a full pattern match is required (else a pattern match as far as the given base
	 * path goes is sufficient)
	 * @return bool <code>true</code> if the supplied <code>path</code> matched
	 */
	protected function doMatch($pattern, $path, $fullMatch) {
		
		if (Strings::startsWith($path, $this->pathSeparator) !== Strings::startsWith($pattern, $this->pathSeparator)) {
			return false;
		}

		$pattDirs = Strings::tokenizeToArray($pattern, $this->pathSeparator);
		$pathDirs = Strings::tokenizeToArray($path, $this->pathSeparator);
		
		$pattIdxStart = 0;
		$pattIdxEnd = count($pattDirs) - 1;
		$pathIdxStart = 0;
		$pathIdxEnd = count($pathDirs) - 1;

		// Match all elements up to the first **
		while ($pattIdxStart <= $pattIdxEnd && $pathIdxStart <= $pathIdxEnd) {
			$patDir = $pattDirs[$pattIdxStart];
			if ("**" === $patDir) {
				break;
			}
			if (!$this->matchStrings($patDir, $pathDirs[$pathIdxStart])) {
				return false;
			}
			$pattIdxStart++;
			$pathIdxStart++;
		}

		if ($pathIdxStart > $pathIdxEnd) {
			// Path is exhausted, only match if rest of pattern is * or **'s
			if ($pattIdxStart > $pattIdxEnd) {
				return (Strings::endsWith($pattern, $this->pathSeparator) ?
						Strings::endsWith($path, $this->pathSeparator) : !Strings::endsWith($path, $this->pathSeparator));
			}
			if (!$fullMatch) {
				return true;
			}
			if ($pattIdxStart === $pattIdxEnd && $pattDirs[$pattIdxStart]=== '*' &&
					Strings::endsWith($path, $this->pathSeparator)) {
				return true;
			}
			for ($i = $pattIdxStart; $i <= $pattIdxEnd; $i++) {
				if ($pattDirs[$i] !== '**') {
					return false;
				}
			}
			return true;
		}
		else if ($pattIdxStart > $pattIdxEnd) {
			// String not exhausted, but pattern is. Failure.
			return false;
		}
		else if (!$fullMatch && '**' === $pattDirs[$pattIdxStart]) {
			// Path start definitely matches due to "**" part in pattern.
			return true;
		}

		// up to last '**'
		while ($pattIdxStart <= $pattIdxEnd && $pathIdxStart <= $pathIdxEnd) {
			$patDir = $pattDirs[$pattIdxEnd];
			if ($patDir === '**') {
				break;
			}
			if (!$this->matchStrings($patDir, $pathDirs[$pathIdxEnd])) {
				return false;
			}
			$pattIdxEnd--;
			$pathIdxEnd--;
		}
		if ($pathIdxStart > $pathIdxEnd) {
			// String is exhausted
			for ($i = $pattIdxStart; $i <= $pattIdxEnd; $i++) {
				if ($pattDirs[$i] !== '**') {
					return false;
				}
			}
			return true;
		}

		while ($pattIdxStart != $pattIdxEnd && $pathIdxStart <= $pathIdxEnd) {
			$patIdxTmp = -1;
			for ($i = $pattIdxStart + 1; $i <= $pattIdxEnd; $i++) {
				if ($pattDirs[$i] === '**') {
					$patIdxTmp = $i;
					break;
				}
			}
			if ($patIdxTmp == $pattIdxStart + 1) {
				// '**/**' situation, so skip one
				$pattIdxStart++;
				continue;
			}
			// Find the pattern between padIdxStart & padIdxTmp in str between
			// strIdxStart & strIdxEnd
			$patLength = ($patIdxTmp - $pattIdxStart - 1);
			$strLength = ($pathIdxEnd - $pathIdxStart + 1);
			$foundIdx = -1;

		    for ($i = 0; $i <= $strLength - $patLength; $i++) {
			    for ($j = 0; $j < $patLength; $j++) {
				    $subPat = $pattDirs[$pattIdxStart + $j + 1];
				    $subStr = $pathDirs[$pathIdxStart + $i + $j];
				    if (!$this->matchStrings($subPat, $subStr)) {
					    continue 2;
				    }
			    }
			    $foundIdx = $pathIdxStart + $i;
			    break;
		    }

			if ($foundIdx == -1) {
				return false;
			}

			$pattIdxStart = $patIdxTmp;
			$pathIdxStart = $foundIdx + $patLength;
		}

		for ($i = $pattIdxStart; $i <= $pattIdxEnd; $i++) {
			if ($pattDirs[$i] !== '**') {
				return false;
			}
		}

		return true;
	}

	/**
	 * Tests whether or not a string matches against a pattern.
	 * The pattern may contain two special characters:<br>
	 * '*' means zero or more characters<br>
	 * '?' means one and only one character
	 * @param string $pattern pattern to match against. Must not be <code>null</code>.
	 * @param string $str which must be matched against the pattern. Must not be <code>null</code>.
	 * @return bool <code>true</code> if the string matches against the
	 */
	private function matchStrings($pattern, $str) {
		$patArr = str_split($pattern);
		$strArr = str_split($str);

		$patIdxStart = 0;
		$patIdxEnd = count($patArr) - 1;
		$strIdxStart = 0;
		$strIdxEnd = count($strArr) - 1;

//		$containsStar = false;
//		for ($i = 0; $i < count($patArr); $i++) {
//			if ($patArr[$i] === '*') {
//				$containsStar = true;
//				break;
//			}
//		}
		$containsStar = in_array('*', $patArr);

		$ch = null;
		if (!$containsStar) {
			// No '*'s, so we make a shortcut
			if ($patIdxEnd != $strIdxEnd) {
				return false; // Pattern and string do not have the same size
			}
			for ($i = 0; $i <= $patIdxEnd; $i++) {
				$ch = $patArr[$i];
				if ($ch !== '?') {
					if ($ch !== $strArr[$i]) {
						return false;// Character mismatch
					}
				}
			}
			return true; // String matches against pattern
		}


		if ($patIdxEnd == 0) {
			return true; // Pattern contains only '*', which matches anything
		}

		// Process characters before first star
		while (($ch = $patArr[$patIdxStart]) !== '*' && $strIdxStart <= $strIdxEnd) {
			if ($ch !== '?') {
				if ($ch !== $strArr[$strIdxStart]) {
					return false;// Character mismatch
				}
			}
			$patIdxStart++;
			$strIdxStart++;
		}
		if ($strIdxStart > $strIdxEnd) {
			// All characters in the string are used. Check if only '*'s are
			// left in the pattern. If so, we succeeded. Otherwise failure.
			for ($i = $patIdxStart; $i <= $patIdxEnd; $i++) {
				if ($patArr[$i] !== '*') {
					return false;
				}
			}
			return true;
		}

		// Process characters after last star
		while (($ch = $patArr[$patIdxEnd]) !== '*' && $strIdxStart <= $strIdxEnd) {
			if ($ch !== '?') {
				if ($ch !== $strArr[$strIdxEnd]) {
					return false;// Character mismatch
				}
			}
			$patIdxEnd--;
			$strIdxEnd--;
		}
		if ($strIdxStart > $strIdxEnd) {
			// All characters in the string are used. Check if only '*'s are
			// left in the pattern. If so, we succeeded. Otherwise failure.
			for ($i = $patIdxStart; $i <= $patIdxEnd; $i++) {
				if ($patArr[$i] !== '*') {
					return false;
				}
			}
			return true;
		}

		// process pattern between stars. padIdxStart and patIdxEnd point
		// always to a '*'.
		while ($patIdxStart !== $patIdxEnd && $strIdxStart <= $strIdxEnd) {
			$patIdxTmp = -1;
			for ($i = $patIdxStart + 1; $i <= $patIdxEnd; $i++) {
				if ($patArr[$i] === '*') {
					$patIdxTmp = $i;
					break;
				}
			}
			if ($patIdxTmp === $patIdxStart + 1) {
				// Two stars next to each other, skip the first one.
				$patIdxStart++;
				continue;
			}
			// Find the pattern between padIdxStart & padIdxTmp in str between
			// strIdxStart & strIdxEnd
			$patLength = ($patIdxTmp - $patIdxStart - 1);
			$strLength = ($strIdxEnd - $strIdxStart + 1);
			$foundIdx = -1;

			for ($i = 0; $i <= $strLength - $patLength; $i++) {
				for ($j = 0; $j < $patLength; $j++) {
					$ch = $patArr[$patIdxStart + $j + 1];
					if ($ch !== '?') {
						if ($ch !== $strArr[$strIdxStart + $i + $j]) {
							continue 2;
						}
					}
				}

				$foundIdx = $strIdxStart + $i;
				break;
			}

			if ($foundIdx === -1) {
				return false;
			}

			$patIdxStart = $patIdxTmp;
			$strIdxStart = $foundIdx + $patLength;
		}

		// All characters in the string are used. Check if only '*'s are left
		// in the pattern. If so, we succeeded. Otherwise failure.
		for ($i = $patIdxStart; $i <= $patIdxEnd; $i++) {
			if ($patArr[$i] !== '*') {
				return false;
			}
		}

		return true;
	}

	/**
	 * Given a pattern and a full path, determine the pattern-mapped part.
	 * <p>For example:
	 * <ul>
	 * <li>'<code>/docs/cvs/commit.html</code>' and '<code>/docs/cvs/commit.html</code> -> ''</li>
	 * <li>'<code>/docs/*</code>' and '<code>/docs/cvs/commit</code> -> '<code>cvs/commit</code>'</li>
	 * <li>'<code>/docs/cvs/*.html</code>' and '<code>/docs/cvs/commit.html</code> -> '<code>commit.html</code>'</li>
	 * <li>'<code>/docs/**</code>' and '<code>/docs/cvs/commit</code> -> '<code>cvs/commit</code>'</li>
	 * <li>'<code>/docs/**\/*.html</code>' and '<code>/docs/cvs/commit.html</code> -> '<code>cvs/commit.html</code>'</li>
	 * <li>'<code>/*.html</code>' and '<code>/docs/cvs/commit.html</code> -> '<code>docs/cvs/commit.html</code>'</li>
	 * <li>'<code>*.html</code>' and '<code>/docs/cvs/commit.html</code> -> '<code>/docs/cvs/commit.html</code>'</li>
	 * <li>'<code>*</code>' and '<code>/docs/cvs/commit.html</code> -> '<code>/docs/cvs/commit.html</code>'</li>
	 * </ul>
	 * <p>Assumes that {@link #match} returns <code>true</code> for '<code>pattern</code>'
	 * and '<code>path</code>', but does <strong>not</strong> enforce this.
	 */
	public function extractPathWithinPattern($pattern, $path) {
		$patternParts = Strings::tokenizeToArray($pattern, $this->pathSeparator);
		$pathParts = Strings::tokenizeToArray($path, $this->pathSeparator);
		
		$patternPartLength = count($patternParts);
		$pathPartsLength = count($pathParts);

		$buffer = '';

		// Add any path parts that have a wildcarded pattern part.
		$puts = 0;
		for ($i = 0; $i < $patternPartLength; $i++) {
			$patternPart = $patternParts[$i];

			if ((strpos($patternPart, '*') > -1 || strpos($patternPart, '?') > -1) && $pathPartsLength >= $i + 1) {
				if ($puts > 0 || ($i === 0 && !Strings::startsWith($pattern, $this->pathSeparator))) {
					$buffer .= $this->pathSeparator;
				}
				$buffer .= $pathParts[$i];
				$puts++;
			}
		}

		// Append any trailing path parts.
		for ($i = $patternPartLength; $i < $pathPartsLength; $i++) {
			if ($puts > 0 || $i > 0) {
				$buffer .= $this->pathSeparator;
			}
			$buffer .= $pathParts[$i];
		}

		return $buffer;
	}
}
