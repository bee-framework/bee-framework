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
 * Created by IntelliJ IDEA.
 * User: mp
 * Date: Feb 19, 2010
 * Time: 9:34:09 PM
 * To change this template use File | Settings | File Templates.
 */

class Bee_Security_Intercept_InterceptorStatusToken {

    /**
     * @var Bee_Security_IAuthentication
     */
    private $authentication;

    /**
     * @var Bee_Security_ConfigAttributeDefinition
     */
    private $attr;

    /**
     * @var mixed
     */
    private $secureObject;

    /**
     * @var boolean 
     */
    private $contextHolderRefreshRequired;

    //~ Constructors ===================================================================================================

    public function __construct(Bee_Security_IAuthentication $authentication, $contextHolderRefreshRequired,
        Bee_Security_ConfigAttributeDefinition $attr, $secureObject) {
        $this->authentication = $authentication;
        $this->contextHolderRefreshRequired = $contextHolderRefreshRequired;
        $this->attr = $attr;
        $this->secureObject = $secureObject;
    }

    //~ Methods ========================================================================================================

    /**
     * @return Bee_Security_ConfigAttributeDefinition
     */
    public function getAttr() {
        return $this->attr;
    }

    /**
     * @return Bee_Security_IAuthentication
     */
    public function getAuthentication() {
        return $this->authentication;
    }

    /**
     * @return mixed
     */
    public function getSecureObject() {
        return $this->secureObject;
    }

    /**
     * @return bool
     */
    public function isContextHolderRefreshRequired() {
        return $this->contextHolderRefreshRequired;
    }
}
?>