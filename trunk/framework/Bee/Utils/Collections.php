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
class Collections {
	
	public static function findFirstKeyMatch(array $source, array $candidates) {
		if(self::isEmpty($source) || self::isEmpty($candidates)) {
			return null;
		}
		$intersect = array_intersect_key($candidates, $source);
		if(self::isEmpty($intersect)) {
			return null;
		}
		$intersect = array_keys($intersect);
		return $intersect[0];
	}
	
	public static function isEmpty(array $collection) {
		return (is_null($collection) || count($collection) === 0);
	}
}
