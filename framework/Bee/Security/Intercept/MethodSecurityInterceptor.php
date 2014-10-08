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
 * Date: Feb 19, 2010
 * Time: 10:19:45 PM
 * To change this template use File | Settings | File Templates.
 */

class Bee_Security_Intercept_MethodSecurityInterceptor extends Bee_Security_Intercept_AbstractSecurityInterceptor implements Bee_AOP_Intercept_IMethodInterceptor {

    /**
     * @var Bee_Security_Intercept_IMethodDefinitionSource
     */
    private $objectDefinitionSource;

    /**
     * Gets the ObjectDefinitionSource
     *
     * @return Bee_Security_Intercept_IMethodDefinitionSource $objectDefinitionSource
     */
    public function getObjectDefinitionSource() {
        return $this->objectDefinitionSource;
    }

    /**
     * Sets the ObjectDefinitionSource
     *
     * @param $objectDefinitionSource Bee_Security_Intercept_IMethodDefinitionSource
     * @return void
     */
    public function setObjectDefinitionSource(Bee_Security_Intercept_IMethodDefinitionSource $objectDefinitionSource) {
        $this->objectDefinitionSource = $objectDefinitionSource;
    }

    public function obtainObjectDefinitionSource() {
        return $this->objectDefinitionSource;
    }

    //~ Methods ========================================================================================================
    

    public function getSecureObjectClassName() {
        return 'Bee_AOP_Intercept_IMethodInvocation';
    }

	/**
	 * This method should be used to enforce security on a <code>MethodInvocation</code>.
	 *
	 * @param Bee_AOP_Intercept_IMethodInvocation $mi The $mi method being invoked which requires a security decision
	 * @throws Bee_Security_Exception_AccessDenied
	 * @throws Exception
	 *
	 * @return mixed The returned value from the method invocation
	 *
	 */
    public function invoke(Bee_AOP_Intercept_IMethodInvocation $mi) {
        $result = null;
        $token = parent::beforeInvocation($mi);

        try {

            $result = $mi->proceed();

            $result = parent::afterInvocation($token, $result);
        } catch (Exception $ex) {
            $result = parent::afterInvocation($token, $result);
            throw $ex;
        }

        return $result;
    }
}