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

    public static $DEFAULT_ASCII_REPLACEMENTS_TABLE = array(
        'ä' => 'ae',
        'ö' => 'oe',
        'ü' => 'ue',
        'Ä' => 'Ae',
        'Ö' => 'Oe',
        'Ü' => 'Ue',
        'ß' => 'ss'
    );

	/**
	 * Enter description here...
	 *
	 * @param String $text
	 * @param bool $strict
	 * @return boolean
	 */
	public static function hasLength($text, $strict=false) {
		if(is_null($text)) {
			return false;
		}
        if ($strict) {
            self::checkIsString($text);
        }
		return (mb_strlen($text) > 0);
	}

	/**
	 * @param $text
	 * @param bool $strict
	 * @return bool
	 */
	public static function hasText($text, $strict=false) {
        if(is_null($text)) {
            return false;
        }
        if ($strict) {
            self::checkIsString($text);
        }
		return self::hasLength(trim(strval($text)));
	}

    private static function checkIsString($text) {
        if ($text !== false && $text !== null && !is_string($text)) {
            throw new Bee_Exceptions_TypeMismatch(Bee_Utils_ITypeDefinitions::STRING, Bee_Utils_Types::getType($text));
        }
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

	/**
	 * @param $string
	 * @param $prefix
	 * @param bool $strict
	 * @return bool
	 */
	public static function startsWith($string, $prefix, $strict=false) {
        if(is_null($string)) {
            return is_null($prefix);
        }
        if(is_null($prefix)) {
            return false;
        }
        if ($strict) {
            self::checkIsString($string);
            self::checkIsString($prefix);
        }
        $prefTest = substr($string, 0, strlen($prefix));
		return $prefTest === false ? "" === $prefix : $prefTest === $prefix;
	}

	/**
	 * Strip $prefix from $str and return $str. If $prefix is not present, original $str is returned.
	 *
	 * @param String $str
	 * @param String $prefix
	 * @return String
	 */
	public static function stripPrefix($str, $prefix) {
		if(is_null($str)) {
			return null;
		}
		if(is_null($prefix) || strlen($prefix) === 0) {
			return $str;
		}
		if (self::startsWith($str, $prefix)) {
			$str = substr($str, strlen($prefix));
		}
		return $str;
	}

	/**
	 * @param $string
	 * @param $suffix
	 * @param bool $strict
	 * @return bool
	 */
	public static function endsWith($string, $suffix, $strict=false) {
		if(is_null($string)) {
			return is_null($suffix);
		}
		if(is_null($suffix)) {
			return false;
		}
        if ($strict) {
            self::checkIsString($string);
            self::checkIsString($suffix);
        }
		$sufTest = substr($string, (-strlen($suffix)));
		return $sufTest === $string || $sufTest === false ? "" === $suffix : $sufTest === $suffix;
	}

    /**
     * Creates ascii-slug from any string. Make sure iconv is installed and setLocale() is called before using this
     * E.g. setlocale(LC_ALL, 'en_US.UTF8');
     *
     * @param $string
     * @param array $table
     * @param string $delimiter
     * @param bool $toLowercase
     *
     * @return string
     */
    public static function toAscii($string, $table=array(), $delimiter='-', $toLowercase=true, $allowEmailChars=false) {
        if (count($table)>0) {
            $string = strtr($string, $table);
       	}
       	$ascii = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
        $emailChars = $allowEmailChars ? '@.' : '';
        $ascii = preg_replace("/[^a-zA-Z0-9\/_|+ -".$emailChars."]/", '', $ascii);
        $ascii = trim($ascii, '-');
        $ascii = preg_replace("/[\/_|+ -]+/", $delimiter, $ascii);
       	return $toLowercase ? strtolower($ascii) : $ascii;
	}
}
