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
 * Created by PhpStorm.
 * User: bugs
 * Date: 20.07.2010
 * Time: 14:52:22
 * To change this template use File | Settings | File Templates.
 */
class Bee_Utils_UserAgent {

    private $name;
    private $version;
    private $majorVersion;
    private $minorVersion;

    /**
     * Gets the Name
     *
     * @return  $name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the Name
     *
     * @param $name
     * @return void
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Gets the MajorVersion
     *
     * @return  $majorVersion
     */
    public function getMajorVersion() {
        return $this->majorVersion;
    }

    /**
     * Gets the MinorVersion
     *
     * @return  $minorVersion
     */
    public function getMinorVersion() {
        return $this->minorVersion;
    }

    /**
     * Gets the Version
     *
     * @return  $version
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * Sets the Version
     *
     * @param $version
     * @return void
     */
    public function setVersion($version) {
        $version = floatval($version);
        $this->version = $version;
        $this->majorVersion = intval(floor($version));
        $this->minorVersion = intval(floor(($this->version - $this->majorVersion)*10));
    }


}
 
