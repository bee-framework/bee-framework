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
use Bee\Security\ConfigAttribute;
use Bee\Security\ConfigAttributeDefinition;
use Bee\Security\IAuthentication;
use Bee\Security\IRunAsManager;

/**
 * Class Bee_Security_RunAs_NullRunAsManager
 */
class Bee_Security_RunAs_NullRunAsManager implements IRunAsManager {

    public function buildRunAs(IAuthentication $authentication, $object, ConfigAttributeDefinition $config) {
        return null;
    }

    public function supports(ConfigAttribute $attribute) {
        return false;
    }

    public function supportsClass($classOrClassName) {
        return true;
    }
}