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
 * Date: Mar 23, 2010
 * Time: 11:11:15 AM
 * To change this template use File | Settings | File Templates.
 */
class Bee_Security_Helper {

    /**
     * @var Bee_Security_IAccessDecisionManager
     */
    private static $accessDecisionManager;

    /**
     * @var Bee_Security_IAfterInvocationManager
     */
    private static $afterInvocationProviderManager;

    public static function construct(Bee_Security_IAccessDecisionManager $accessDecisionManager,
                                     Bee_Security_IAfterInvocationManager $afterInvocationProviderManager) {
        self::$accessDecisionManager = $accessDecisionManager;
        self::$afterInvocationProviderManager = $afterInvocationProviderManager;
    }

    public static function isAuthenticated() {
        $auth = Bee_Security_Context_Holder::getContext()->getAuthentication();
        if(is_null($auth) || !$auth->isAuthenticated()) {
            return array();
        }
        return $auth->isAuthenticated();
    }

    public static function getRoles() {
        $auth = Bee_Security_Context_Holder::getContext()->getAuthentication();
        if(is_null($auth) || !$auth->isAuthenticated()) {
            return array();
        }
        return (array_keys($auth->getAuthorities()));
    }

    public static function checkRole($role) {
        $auth = Bee_Security_Context_Holder::getContext()->getAuthentication();
        if(is_null($auth) || !$auth->isAuthenticated()) {
            throw new Bee_Security_Exception_Authentication('Not authenticated');
        }
        self::$accessDecisionManager->decide($auth, null, new Bee_Security_ConfigAttributeDefinition($role));
        return true;
    }

    public static function checkAccess($configAttribute, $secureObject = null) {
        $auth = Bee_Security_Context_Holder::getContext()->getAuthentication();
        if(is_null($auth) || !$auth->isAuthenticated()) {
            throw new Bee_Security_Exception_Authentication('Not authenticated');
        }
        self::$accessDecisionManager->decide($auth, $secureObject, new Bee_Security_ConfigAttributeDefinition($configAttribute));
        return true;
    }

    public static function checkResultAccess($configAttribute, $secureObject = null, $returnedObject = null) {
        $auth = Bee_Security_Context_Holder::getContext()->getAuthentication();
        if(is_null($auth) || !$auth->isAuthenticated()) {
            throw new Bee_Security_Exception_Authentication('Not authenticated');
        }
        return self::$afterInvocationProviderManager->decide($auth, $secureObject, new Bee_Security_ConfigAttributeDefinition($configAttribute), $returnedObject);
    }
}

class SEC extends Bee_Security_Helper {}
