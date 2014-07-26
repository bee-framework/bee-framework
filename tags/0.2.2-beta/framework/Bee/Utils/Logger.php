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
 * Time: 4:59:15 AM
 * To change this template use File | Settings | File Templates.
 */
class Bee_Utils_Logger {

    private static $path = 'beelog';
    private static $csvSeparator = ';';

    private static $debugEnabled = false;

    /**
     * Gets the Path
     *
     * @return  $path
     */
    public function getPath() {
        return self::$path;
    }

    /**
     * Sets the Path
     *
     * @param $path
     * @return void
     */
    public function setPath($path) {
        self::$path = $path;
    }

    /**
     * Gets the CsvSeparator
     *
     * @return  $csvSeparator
     */
    public function getCsvSeparator() {
        return self::$csvSeparator;
    }

    /**
     * Sets the CsvSeparator
     *
     * @param $csvSeparator
     * @return void
     */
    public function setCsvSeparator($csvSeparator) {
        self::$csvSeparator = $csvSeparator;
    }

    public static function isDebugEnabled() {
        return self::$debugEnabled;
    }

    public static function setDebugEnabled($debugEnabled) {
        self::$debugEnabled = $debugEnabled;
    }

    public static function debug($message) {
        if (self::isDebugEnabled()) {
            error_log($message, E_USER_NOTICE);
        }
    }

    public static function info($message) {
        error_log($message, E_USER_NOTICE);
    }

    public static function warn($message, Exception $ex = null) {
        if($ex) {
            $ex = self::getRootCause($ex);
            error_log($message . ' - ' . $ex->getMessage(), E_USER_WARNING);
        } else {
            error_log($message, E_USER_WARNING);
        }
    }

    public static function error($message, Exception $ex = null) {
        if($ex) {
            $ex = self::getRootCause($ex);
            error_log($message . ' - ' . $ex->getMessage(), E_USER_WARNING);
        } else {
            error_log($message, E_USER_WARNING);
        }
    }

    /**
     * @static
     * @param string|array $message
     * @param string $filename
     * @param bool $timestamp
     * @return void
     */
    public static function toFile($message, $filename="log.txt", $timestamp=true) {
        if (is_array($message)) {
            if ($timestamp) {
                array_unshift($message, date('H:i:s'));
                array_unshift($message, date('Y-m-d'));
            }
            $message = implode(self::$csvSeparator, $message);
        } else if ($timestamp) {
            $message = date('H:i:s').' | '.$message;
            $message = date('Y-m-d').' - '.$message;
        }
        $message .= "\n";

        $fn = self::$path;
        if (mb_strlen($fn)>0 && $fn[mb_strlen($fn)]!=DIRECTORY_SEPARATOR) {
            $fn .= DIRECTORY_SEPARATOR;
        }

        $bn = pathinfo($filename, PATHINFO_DIRNAME);
        if (Bee_Utils_Strings::hasText($bn) && $bn!=".") {
            $fn .= $bn;
            if (mb_strlen($fn)>0 && $fn[mb_strlen($fn)]!=DIRECTORY_SEPARATOR) {
                $fn .= DIRECTORY_SEPARATOR;
            }
        }

        $fn .= pathinfo($filename, PATHINFO_FILENAME);
        $fn .= "_".date("Y-m-d");
        $fn .= ".".pathinfo($filename, PATHINFO_EXTENSION);

        file_put_contents($fn, $message, FILE_APPEND);
    }

    private static function getRootCause(Exception $ex) {
        while($ex instanceof Bee_Exceptions_Base && !is_null($ex->getCause())) {
            $ex = $ex->getCause();
        }
        return $ex;
    }
}

/**
 * Convenient alternative name
 *
 * @author bugs
 *
 */
class LOG extends Bee_Utils_Logger {
}
