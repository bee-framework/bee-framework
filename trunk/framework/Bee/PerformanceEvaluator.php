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
 * @deprecated
 */
final class Bee_PerformanceEvaluator {
	
	public static $intervals = array();
	
	
	
	/**
	 * Adds a named interval with the current timestamp
	 * measured in microseconds.
	 *
	 * @Return void
	 * @param String $name
	 */
	public static function addInterval($name) {
		self::$intervals[$name] = microtime(true);
	}
	
	
	
	/**
	 * Calculates how long each interval lasted.
	 *
	 * @return Array
	 */
	public static function evaluate() {
		$evalInts = array();
		$count = 0;
		foreach (self::$intervals as $key => $value) {
			if ($count>0) {
				$evalInts[$key] = $value-$prevVal;
			} else {
				$initVal = $value;
			}
			$prevKey = $key;
			$prevVal = $value;
			$count++;
		}
		$evalInts["Overall processing"] = $value-$initVal;
		return $evalInts;
	}

	
	
	/**
	 * Calculates how long each interval lasted and returns HTML
	 * code to display the result. Parameter decimals sets the number 
	 * of decimal points. 
	 *
	 * @return String
	 * @Param int decimals
	 */
	public static function getEvaluationAsHtml($decimals=5) {
		$result  = "Performance evaluation:<br/>";
		$result .= "<table>";
		foreach (self::evaluate() as $key => $value) {
			$result .= "<tr style=\"border-bottom: solid 1px #000\">";
			$result .= "<td style=\"padding: 5px;\">$key</td>";
			$result .= "<td style=\"padding: 5px;\">".number_format($value, intval($decimals), ",", ".")." s</td>";
			$result .= "</tr>";
		}
		$result .= "</table>";
		return $result;
	}
	
	public static function reset() {
		self::$intervals = array();
	}
}

?>