<?php
/*
 * Copyright 2008-2010 the original author or authors.
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
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class Bee_Utils_Strings {
	
	/**
	 * Enter description here...
	 *
	 * @param String $text
	 * @return boolean
	 */
	public static function hasLength($text) {
		if(is_null($text)) {
			return false;
		}
		if (!is_string($text)) {
			throw new Bee_Exceptions_TypeMismatch(Bee_Utils_ITypeDefinitions::STRING, Bee_Utils_Types::getType($text));
		}
		return (strlen($text) > 0);
	}

	public static function hasText($text) {
		return self::hasLength(trim($text));
	}
	
	
	/**
	 * Tokenize the given String into a associative set (using the tokens as keys and <code>true</code> as value).
	 * <p>The given delimiters string is supposed to consist of any number of
	 * delimiter characters. Each of those characters can be used to separate
	 * tokens. A delimiter is always a single character.
	 *
	 * @param String $str the String to tokenize
	 * @param String $delimiters the delimiter characters, assembled as String
	 * (each of those characters is individually considered as delimiter)
	 * @param boolean $trimTokens trim the tokens via <code>trim</code>
	 * @param boolean $ignoreEmptyTokens omit empty tokens from the result array
	 * @return array an associative array of the tokens (<code>null</code> if the input String was <code>null</code>)
	 */
	public static function tokenizeToArrayKeys($str, $delimiters, $trimTokens = true, $ignoreEmptyTokens = true) {
		if (is_null($str)) {
			return null;
		}

		$result = array();
		if(Bee_Utils_Strings::hasLength($str)) {
			$tok = strtok($str, $delimiters);
			while ($tok !== false) {
				if($trimTokens) {
					$tok = trim($tok);
				}
				if(!$ignoreEmptyTokens || strlen($tok) > 0) {
					$result[$tok] = true;
				}
				$tok = strtok($delimiters);
			}
		}
		return $result;
	}	


	/**
	 * Tokenize the given String into an array of strings.
	 * <p>The given delimiters string is supposed to consist of any number of
	 * delimiter characters. Each of those characters can be used to separate
	 * tokens. A delimiter is always a single character.
	 *
	 * @param String $str the String to tokenize
	 * @param String $delimiters the delimiter characters, assembled as String
	 * (each of those characters is individually considered as delimiter)
	 * @param boolean $trimTokens trim the tokens via <code>trim</code>
	 * @param boolean $ignoreEmptyTokens omit empty tokens from the result array
	 * @return array an array of the tokens (<code>null</code> if the input String was <code>null</code>)
	 */
	public static function tokenizeToArray($str, $delimiters, $trimTokens = true, $ignoreEmptyTokens = true) {
		if (is_null($str)) {
			return null;
		}

		$result = array();
		if(Bee_Utils_Strings::hasLength($str)) {
			$tok = strtok($str, $delimiters);
			while ($tok !== false) {
				if($trimTokens) {
					$tok = trim($tok);
				}
				if(!$ignoreEmptyTokens || strlen($tok) > 0) {
					$result[] = $tok;
				}
				$tok = strtok($delimiters);
			}
		}
		return $result;
	}	
	
	public static function startsWith($string, $prefix) {
		return substr($string, 0, strlen($prefix)) === $prefix;
	}

	/**
	 * Strip $prefix from $str and return $str. If $prefix is not present, original $str is returned.
	 *
	 * @param String $str
	 * @param String $prefix
	 * @return String
	 */
	public static function stripPrefix($str, $prefix) {
		if (is_string($str) && substr_count($str, $prefix)>0) {
			$str = str_replace($prefix, '', $str);
		}
		return $str;
	}
	
	public static function endsWith($string, $suffix) {
		return substr($string, (-strlen($suffix))) === $suffix;
	}
	
}
?>