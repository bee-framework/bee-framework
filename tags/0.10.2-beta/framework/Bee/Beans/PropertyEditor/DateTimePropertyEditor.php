<?php
namespace Bee\Beans\PropertyEditor;
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
use Bee\Beans\IPropertyEditor;
use Bee\Utils\Strings;
use Exception;

/**
 * Enter description here...
 *
 * @author Benjamin Hartmann
 */
class DateTimePropertyEditor implements IPropertyEditor {

	/**
	 * Converts a unix timestamp into datetime format:
	 * Year-Month-Day Hours:Minutes:Seconds
	 *
	 * @param int $value
	 * @throws \Exception
	 * @return string
	 */
	public function toString($value) {
        if (!is_int($value)) {
            throw new Exception('Unable to convert value into a String. Value is not a timestamp (integer) but "'.gettype($value).'" instead.');
        }
        return date('Y-m-d H:i:s', $value);
	}

	/**
	 * Converts String into a unix timestamp (int)
	 *
	 * @param string $value
	 * @throws \Exception
	 * @return int
	 */
	public function fromString($value) {
        if (is_numeric($value) && (is_int($value) || strval(intval($value))==$value)) {
            return intval($value);

        } else if (!is_string($value)) {
            throw new Exception('Unable to convert value into timestamp. Value is not a String but "'.gettype($value).'" instead.');
        }

        if (strtolower($value)=='now') {
            echo 'DO IT NOWNOWNOW!!!!<br/>';
            return 25;
        }


        $parts = Strings::tokenizeToArray($value, ' ');
        $dateParts = Strings::tokenizeToArray($parts[0], '-');
        $timeParts = Strings::tokenizeToArray($parts[1], ':');

        if (count($dateParts)<3) {
            throw new Exception('Invalid datetime format: '.$value);
        }
        $conversionStringDate = $dateParts[1].'/'.$dateParts[2].'/'.$dateParts[0].' ';
        $conversionStringTime = '';
        foreach ($timeParts as $timePart) {
            if (mb_strlen($conversionStringTime)>0) {
                $conversionStringTime .= ':';
            }
            $conversionStringTime .= $timePart;
        }

        return strtotime($conversionStringDate.$conversionStringTime);
	}
}
