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
 * Date: Mar 19, 2010
 * Time: 5:30:14 PM
 * To change this template use File | Settings | File Templates.
 */

abstract class Bee_Security_Vote_AbstractAclVoter implements Bee_Security_Vote_IAccessDecisionVoter {

    /**
     * @var string
     */
    private $processDomainObjectClass;

    protected function getDomainObjectInstance($secureObject) {
//        Object[] args;
//        Class[] params;
//
//        if ($secureObject instanceof MethodInvocation) {
//            MethodInvocation invocation = (MethodInvocation) secureObject;
//            params = invocation.getMethod().getParameterTypes();
//            args = invocation.getArguments();
//        } else if($secureObject instanceof JoinPoint) {
//            JoinPoint jp = (JoinPoint) secureObject;
//            params = ((CodeSignature) jp.getStaticPart().getSignature()).getParameterTypes();
//            args = jp.getArgs();
//        } else {
            return $secureObject;
//        }
//
//        for (int i = 0; i < params.length; i++) {
//            if (processDomainObjectClass.isAssignableFrom(params[i])) {
//                return args[i];
//            }
//        }
//
//        throw new AuthorizationServiceException("Secure object: " + secureObject
//            + " did not provide any argument of type: " + processDomainObjectClass);
    }

    public function getProcessDomainObjectClass() {
        return $this->processDomainObjectClass;
    }

    /**
     * @param string $processDomainObjectClass
     * @return void
     */
    public function setProcessDomainObjectClass($processDomainObjectClass) {
        Bee_Utils_Assert::hasText($processDomainObjectClass, 'processDomainObjectClass cannot be empty');
        $this->processDomainObjectClass = $processDomainObjectClass;
    }

    /**
     * This implementation supports only <code>MethodSecurityInterceptor</code>, because it queries the
     * presented <code>MethodInvocation</code>.
     *
     * @param clazz the secure object
     *
     * @return <code>true</code> if the secure object is <code>MethodInvocation</code>, <code>false</code> otherwise
     */
    public function supportsClass($className) {
        return true; // todo
//        if (MethodInvocation.class.isAssignableFrom(clazz)) {
//            return true;
//        } else if (JoinPoint.class.isAssignableFrom(clazz)) {
//            return true;
//        } else {
//            return false;
//        }
    }
}
?>