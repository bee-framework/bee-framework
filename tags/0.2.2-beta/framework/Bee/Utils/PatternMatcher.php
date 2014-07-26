<?php
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
 * Created by IntelliJ IDEA.
 * User: mp
 * Date: Feb 18, 2010
 * Time: 2:54:05 AM
 * To change this template use File | Settings | File Templates.
 */

class Bee_Utils_PatternMatcher {

    /**
     * Match a String against the given pattern, supporting the following simple
     * pattern styles: "xxx*", "*xxx", "*xxx*" and "xxx*yyy" matches (with an
     * arbitrary number of pattern parts), as well as direct equality.
     * @param string $pattern the pattern to match against
     * @param string $str the String to match
     * @return whether the String matches the given pattern
     */
    public static function simpleMatch($pattern, $str) {
        if ($pattern == null || $str == null) {
            return false;
        }

        $firstIndex = strpos($pattern, '*');
        if ($firstIndex == -1) {
            return $pattern == $str;
        }
        if ($firstIndex == 0) {
            if (strlen($pattern) == 1) {
                return true;
            }
            $nextIndex = strpos($pattern, '*', $firstIndex + 1);
            if ($nextIndex == -1) {
                return Bee_Utils_Strings::endsWith($str, substr($pattern, 1));
            }
            $part = substr($pattern, 1, $nextIndex - 1);
            $partIndex = strpos($str, $part);
            while ($partIndex != -1) {
                if (self::simpleMatch(substr($pattern, $nextIndex), substr($str, $partIndex + strlen($part)))) {
                    return true;
                }
                $partIndex = strpos($str, $part, $partIndex + 1);
            }
            return false;
        }
        return (strlen($str) >= $firstIndex && substr($pattern, 0, $firstIndex) == substr($str, 0, $firstIndex) &&
                self::simpleMatch(substr($pattern, $firstIndex), substr($str, $firstIndex)));
    }

    /**
     * Match a String against the given patterns, supporting the following simple
     * pattern styles: "xxx*", "*xxx", "*xxx*" and "xxx*yyy" matches (with an
     * arbitrary number of pattern parts), as well as direct equality.
     * @param string[] $patterns the patterns to match against
     * @param string $str the String to match
     * @return whether the String matches any of the given patterns
     */
    public static function simpleMatchMultiple(array $patterns, $str) {
        if ($patterns != null) {
            foreach($patterns as $pattern) {
                if (self::simpleMatch($pattern, $str)) {
                    return true;
                }
            }
        }
        return false;
    }

}
