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
 * Date: Feb 18, 2010
 * Time: 12:30:07 AM
 * To change this template use File | Settings | File Templates.
 */

class Bee_Transactions_Interceptor_TransactionInterceptor extends Bee_Transactions_Interceptor_TransactionAspectSupport implements Bee_AOP_Intercept_IMethodInterceptor {

    /**
     * Create a new TransactionInterceptor.
     * @param ptm the transaction manager to perform the actual transaction management
     * @param tas the attribute source to be used to find transaction attributes
     * @see #setTransactionManager
     * @see #setTransactionAttributeSource(TransactionAttributeSource)
     */
    public function __construct(Bee_Transactions_IPlatformTransactionManager $ptm, Bee_Transactions_Interceptor_ITransactionAttributeSource $tas) {
        $this->setTransactionManager($ptm);
        $this->setTransactionAttributeSource($tas);
    }


    public function invoke(Bee_AOP_Intercept_IMethodInvocation $invocation) {
        // Work out the target class: may be <code>null</code>.
        // The TransactionAttributeSource should be passed the target class
        // as well as the method, which may be from an interface.
        $targetClassName = ($invocation->getThis() != null ? get_class($invocation->getThis()) : null);

        // If the transaction attribute is null, the method is non-transactional.
        $txAttr = $this->getTransactionAttributeSource()->getTransactionAttribute($invocation->getMethod(), $targetClassName);
        $joinpointIdentification = $this->methodIdentification($invocation->getMethod());

        // Standard transaction demarcation with getTransaction and commit/rollback calls.
        $txInfo = $this->createTransactionFromAttributeIfNecessary($txAttr, $joinpointIdentification);
        $retVal = null;
        try {
            // This is an around advice: Invoke the next interceptor in the chain.
            // This will normally result in a target object being invoked.
            $retVal = $invocation->proceed();
            $this->cleanupTransactionInfo($txInfo);
        }
        catch (Exception $ex) {
            // target invocation exception
            $this->completeTransactionAfterThrowing($txInfo, $ex);
            $this->cleanupTransactionInfo($txInfo);
            throw $ex;
        }
        $this->commitTransactionAfterReturning($txInfo);
        return $retVal;
    }
}
