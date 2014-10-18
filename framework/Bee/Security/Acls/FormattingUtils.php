<?php
namespace Bee\Security\Acls;
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
 * Class FormattingUtils
 * @package Bee\Security\Acls
 */
class FormattingUtils {

    private static function printBinary($i, $on, $off) {
        $s = '';
        while($i > 0) {
            $s = ($i & 1) . $s;
            $i >>= 1;
        }
        $pattern = IPermission::THIRTY_TWO_RESERVED_OFF;
        $temp2 = substr($pattern, 0, strlen($pattern) - strlen($s)) . $s;

        return str_replace(array('0', '1'), array($off, $on), $temp2);
    }

    /**
     * Returns a representation of the active bits in the presented mask, with each active bit being denoted by
     * the passed character.
     * <p>
     * Inactive bits will be denoted by character {@link Permission#RESERVED_OFF}.
     *
     * @param integer $mask the integer bit mask to print the active bits for
     * @param string $code the character to print when an active bit is detected
     *
     * @return string a 32-character representation of the bit mask
     */
    public static function printActiveBinary($mask, $code) {

        return self::printBinary($mask, $code, IPermission::RESERVED_OFF);
//        return str_replace(IPermission::RESERVED_ON, $code, self::printBinary($mask,
//            IPermission::RESERVED_ON, IPermission::RESERVED_OFF));
    }

    /**
     * @static
     * @param string $original
     * @param string $extraBits
     * @return string
     */
    public static function mergePatterns($original, $extraBits) {
        $result = '';
        for($i = 0; $i < strlen($extraBits); $i++) {
            $extraBit = substr($extraBits, $i, 1);
            if($extraBit == IPermission::RESERVED_OFF) {
                $result .= substr($original, $i, 1);
            } else {
                $result .= $extraBit;
            }
        }
        return $result;
    }
}