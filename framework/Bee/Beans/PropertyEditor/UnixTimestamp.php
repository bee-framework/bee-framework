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
 * User: mp
 * Date: 14.11.12
 * Time: 16:50
 */

class Bee_Beans_PropertyEditor_UnixTimestamp implements Bee_Beans_IPropertyEditor {

	const REGEX = '/^(\d\d\d\d-\d\d-\d\d \d\d:\d\d:\d\d)(?: (.+))?$/';
	const FORMAT = 'Y-m-d H:i:s e';

	/**
	 * Enter description here...
	 *
	 * @param mixed $value
	 * @return String
	 */
	public function toString($value) {
		$dt = new DateTime("@$value");
		return $dt->format(self::FORMAT);
	}

	/**
	 * Enter description here...
	 *
	 * @param String $value
	 * @return mixed
	 */
	public function fromString($value) {
		$matches = array();
		$res = preg_match(self::REGEX, $value, $matches);

		$datetime = null;
		if($res) {
			if(count($matches) == 3) {
				$datetime = new DateTime($matches[1], new DateTimeZone($matches[2]));
			} else {
				$datetime = new DateTime($matches[1]);
			}
		}

		if(!$datetime) {
			throw new Exception(get_class($this). " could not convert value '$value' to a timestamp");
		}
		return intval($datetime->format('U'));
	}
}
