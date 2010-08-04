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
 * Time: 2:28:01 AM
 * To change this template use File | Settings | File Templates.
 */

class Bee_Transactions_Interceptor_TransactionAspectSupport implements Bee_Context_Config_IInitializingBean {
    /**
     * Holder to support the <code>currentTransactionStatus()</code> method,
     * and to support communication between different cooperating advices
     * (e.g. before and after advice) if the aspect involves more than a
     * single method (as will be the case for around advice).
     */
    static $transactionInfoHolder = null;


    /**
     * Subclasses can use this to return the current TransactionInfo.
     * Only subclasses that cannot handle all operations in one method,
     * such as an AspectJ aspect involving distinct before and after advice,
     * need to use this mechanism to get at the current TransactionInfo.
     * An around advice such as an AOP Alliance MethodInterceptor can hold a
     * reference to the TransactionInfo throughout the aspect method.
     * <p>A TransactionInfo will be returned even if no transaction was created.
     * The <code>TransactionInfo.hasTransaction()</code> method can be used to query this.
     * <p>To find out about specific transaction characteristics, consider using
     * TransactionSynchronizationManager's <code>isSynchronizationActive()</code>
     * and/or <code>isActualTransactionActive()</code> methods.
     * @return Bee_Transactions_Interceptor_TransactionAspectSupportTransactionInfo TransactionInfo bound to this thread,
     * or <code>null</code> if none
     * @see Bee_Transactions_Interceptor_TransactionAspectSupportTransactionInfo#hasTransaction()
     * @see org.springframework.transaction.support.TransactionSynchronizationManager#isSynchronizationActive()
     * @see org.springframework.transaction.support.TransactionSynchronizationManager#isActualTransactionActive()
     */
    static function currentTransactionInfo()  {
        return self::$transactionInfoHolder;
    }

    /**
     * Return the transaction status of the current method invocation.
     * Mainly intended for code that wants to set the current transaction
     * rollback-only but not throw an application exception.
     * @throws NoTransactionException if the transaction info cannot be found,
     * because the method was invoked outside an AOP invocation context
     * @static
     * @throws NoTransactionException
     * @return Bee_Transactions_ITransactionStatus
     */
    public static function currentTransactionStatus() {
        $info = self::currentTransactionInfo();
        if ($info == null) {
            throw new Bee_Transactions_NoTransactionException("No transaction aspect-managed TransactionStatus in scope");
        }
        return $info->transactionStatus;
    }

    /**
     * Delegate used to create, commit and rollback transactions
     * @var Bee_Transactions_IPlatformTransactionManager
     */
    private $transactionManager;

    /**
     * Helper used to find transaction attributes
     * @var Bee_Transactions_Interceptor_ITransactionAttributeSource
     */
    private $transactionAttributeSource;

    /**
     * Set the transaction manager. This will perform actual
     * transaction management: This class is just a way of invoking it.
     * @param Bee_Transactions_IPlatformTransactionManager $transactionManager
     * @return void
     */
    public function setTransactionManager(Bee_Transactions_IPlatformTransactionManager $transactionManager) {
        $this->transactionManager = $transactionManager;
    }

    /**
     * Return the transaction manager.
     * @return Bee_Transactions_IPlatformTransactionManager
     */
    public function getTransactionManager() {
        return $this->transactionManager;
    }

    /**
     * Set the transaction attribute source which is used to find transaction
     * attributes. If specifying a String property value, a PropertyEditor
     * will create a MethodMapTransactionAttributeSource from the value.
     * @param Bee_Transactions_Interceptor_ITransactionAttributeSource $transactionAttributeSource
     * @return void
     */
    public function setTransactionAttributeSource(Bee_Transactions_Interceptor_ITransactionAttributeSource $transactionAttributeSource) {
        $this->transactionAttributeSource = $transactionAttributeSource;
    }

    /**
     * Return the transaction attribute source.
     * @return Bee_Transactions_Interceptor_ITransactionAttributeSource
     */
    public function getTransactionAttributeSource() {
        return $this->transactionAttributeSource;
    }


    /**
     * Check that required properties were set.
     */
    public function afterPropertiesSet() {
        if ($this->getTransactionManager() == null) {
            throw new InvalidArgumentException("Property 'transactionManager' is required");
        }
        if ($this->getTransactionAttributeSource() == null) {
            throw new InvalidArgumentException(
                    "'transactionAttributeSource' is required: " +
                    "If there are no transactional methods, then don't use a transaction aspect.");
        }
    }


    /**
     * Create a transaction if necessary, based on the given method and class.
     * <p>Performs a default TransactionAttribute lookup for the given method.
     * @param ReflectionMethod $method method about to execute
     * @param string $targetClass class the method is on
     * @return Bee_Transactions_Interceptor_TransactionAspectSupportTransactionInfo a TransactionInfo object, whether or
     * not a transaction was created. The hasTransaction() method on TransactionInfo can be used to tell if there
     * was a transaction created.
     * @see #getTransactionAttributeSource()
     */
    protected function createTransactionIfNecessary(ReflectionMethod $method, $targetClassName) {
        // If the transaction attribute is null, the method is non-transactional.
        $txAttr = $this->getTransactionAttributeSource()->getTransactionAttribute($method, $targetClassName);
        return $this->createTransactionFromAttributeIfNecessary($txAttr, $this->methodIdentification($method));
    }

    /**
     * Convenience method to return a String representation of this Method
     * for use in logging. Can be overridden in subclasses to provide a
     * different identifier for the given method.
     * @param method the method we're interested in
     * @return string log message identifying this method
     * @see org.springframework.util.ClassUtils#getQualifiedMethodName
     */
    protected function methodIdentification(ReflectionMethod $method) {
        return $method->getDeclaringClass()->getName() .'.'.$method->getName();
    }

    /**
     * Create a transaction if necessary based on the given TransactionAttribute.
     * <p>Allows callers to perform custom TransactionAttribute lookups through
     * the TransactionAttributeSource.
     * @param Bee_Transactions_Interceptor_ITransactionAttribute $txAttr the TransactionAttribute (may be <code>null</code>)
     * @param string $joinpointIdentification the fully qualified method name
     * (used for monitoring and logging purposes)
     * @return Bee_Transactions_Interceptor_TransactionAspectSupportTransactionInfo a TransactionInfo object, whether or not a transaction was created.
     * The <code>hasTransaction()</code> method on TransactionInfo can be used to
     * tell if there was a transaction created.
     * @see #getTransactionAttributeSource()
     */
    protected function createTransactionFromAttributeIfNecessary(
            Bee_Transactions_Interceptor_ITransactionAttribute $txAttr, $joinpointIdentification) {

        // If no name specified, apply method identification as transaction name.
        if ($txAttr != null && $txAttr->getName() == null) {
            $txAttr = new Bee_Transactions_Interceptor_DelegatingTransactionAttribute($txAttr, $joinpointIdentification);
        }

        $status = null;
        if ($txAttr != null) {
            $tm = $this->getTransactionManager();
            if ($tm != null) {
                $status = $tm->getTransaction($txAttr);
            }
        }
        return $this->prepareTransactionInfo($txAttr, $joinpointIdentification, $status);
    }

    /**
     * Prepare a TransactionInfo for the given attribute and status object.
     * @param Bee_Transactions_Interceptor_ITransactionAttribute $txAttr the TransactionAttribute (may be <code>null</code>)
     * @param string $joinpointIdentification the fully qualified method name
     * (used for monitoring and logging purposes)
     * @param Bee_Transactions_ITransactionStatus $status the TransactionStatus for the current transaction
     * @return Bee_Transactions_Interceptor_TransactionAspectSupportTransactionInfo the prepared TransactionInfo object
     */
    protected function prepareTransactionInfo(
            Bee_Transactions_Interceptor_ITransactionAttribute $txAttr, $joinpointIdentification, Bee_Transactions_ITransactionStatus $status) {

        $txInfo = new Bee_Transactions_Interceptor_TransactionAspectSupportTransactionInfo($txAttr, $joinpointIdentification);
        if ($txAttr != null) {
            // The transaction manager will flag an error if an incompatible tx already exists
            $txInfo->newTransactionStatus($status);
        }
        else {
            // The TransactionInfo.hasTransaction() method will return
            // false. We created it only to preserve the integrity of
            // the ThreadLocal stack maintained in this class.
//            if (logger.isTraceEnabled())
//                logger.trace("Don't need to create transaction for [" + joinpointIdentification +
//                        "]: This method isn't transactional.");
        }

        // We always bind the TransactionInfo to the thread, even if we didn't create
        // a new transaction here. This guarantees that the TransactionInfo stack
        // will be managed correctly even if no transaction was created by this aspect.
        $txInfo->makeCurrent();
        return $txInfo;
    }

    /**
     * Execute after successful completion of call, but not after an exception was handled.
     * Do nothing if we didn't create a transaction.
     * @param Bee_Transactions_Interceptor_TransactionAspectSupportTransactionInfo $txInfo information about the current transaction
     */
    protected function commitTransactionAfterReturning(Bee_Transactions_Interceptor_TransactionAspectSupportTransactionInfo $txInfo) {
        if ($txInfo != null && $txInfo->hasTransaction()) {
//            if (logger.isTraceEnabled()) {
//                logger.trace("Completing transaction for [" + txInfo.getJoinpointIdentification() + "]");
//            }
            $this->getTransactionManager()->commit($txInfo->getTransactionStatus());
        }
    }

    /**
     * Handle a throwable, completing the transaction.
     * We may commit or roll back, depending on the configuration.
     * @param Bee_Transactions_Interceptor_TransactionAspectSupportTransactionInfo $txInfo information about the current transaction
     * @param Exception $ex throwable encountered
     */
    protected function completeTransactionAfterThrowing(Bee_Transactions_Interceptor_TransactionAspectSupportTransactionInfo $txInfo, Exception $ex) {
        if ($txInfo != null && $txInfo->hasTransaction()) {
//            if (logger.isTraceEnabled()) {
//                logger.trace("Completing transaction for [" + txInfo.getJoinpointIdentification() +
//                        "] after exception: " + ex);
//            }
            if ($txInfo->transactionAttribute->rollbackOn($ex)) {
                try {
                    $this->getTransactionManager()->rollback($txInfo->getTransactionStatus());
                }
                catch (Bee_Transactions_TransactionSystemException $ex2) {
//                    logger.error("Application exception overridden by rollback exception", ex);
                    $ex2->initApplicationException($ex);
                    throw $ex2;
                }
                catch (RuntimeException $ex2) {
//                    logger.error("Application exception overridden by rollback exception", ex);
                    throw $ex2;
                }
            }
            else {
                // We don't roll back on this exception.
                // Will still roll back if TransactionStatus.isRollbackOnly() is true.
                try {
                    $this->getTransactionManager()->commit($txInfo->getTransactionStatus());
                }
                catch (Bee_Transactions_TransactionSystemException $ex2) {
//                    logger.error("Application exception overridden by commit exception", ex);
                    $ex2->initApplicationException($ex);
                    throw $ex2;
                }
                catch (RuntimeException $ex2) {
//                    logger.error("Application exception overridden by commit exception", ex);
                    throw $ex2;
                }
            }
        }
    }

    /**
     * Reset the TransactionInfo ThreadLocal.
     * <p>Call this in all cases: exception or normal return!
     * @param Bee_Transactions_Interceptor_TransactionAspectSupportTransactionInfo $txInfo information about the current transaction (may be <code>null</code>)
     */
    protected function cleanupTransactionInfo(Bee_Transactions_Interceptor_TransactionAspectSupportTransactionInfo $txInfo) {
        if ($txInfo != null) {
            $txInfo->restorePrevious();
        }
    }


}

/**
 * Opaque object used to hold Transaction information. Subclasses
 * must pass it back to methods on this class, but not see its internals.
 */
class Bee_Transactions_Interceptor_TransactionAspectSupportTransactionInfo {

    /**
     * @var Bee_Transactions_Interceptor_ITransactionAttribute
     */
    private final $transactionAttribute;

    /**
     * @var string
     */
    private final $joinpointIdentification;

    /**
     * @var Bee_Transactions_ITransactionStatus
     */
    private $transactionStatus;

    /**
     * @var Bee_Transactions_Interceptor_TransactionAspectSupportTransactionInfo
     */
    private $oldTransactionInfo;

    public function __construct(Bee_Transactions_Interceptor_ITransactionAttribute $transactionAttribute, $joinpointIdentification) {
        $this->transactionAttribute = $transactionAttribute;
        $this->joinpointIdentification = $joinpointIdentification;
    }

    /**
     * @return Bee_Transactions_Interceptor_ITransactionAttribute
     */
    public function getTransactionAttribute() {
        return $this->transactionAttribute;
    }

    /**
     * Return a String representation of this joinpoint (usually a Method call) for use in logging.
     * @return string
     */
    public function getJoinpointIdentification() {
        return $this->joinpointIdentification;
    }

    public function newTransactionStatus(Bee_Transactions_ITransactionStatus $status) {
        $this->transactionStatus = $status;
    }

    /**
     * @return Bee_Transactions_ITransactionStatus
     */
    public function getTransactionStatus() {
        return $this->transactionStatus;
    }

    /**
     * Return whether a transaction was created by this aspect, or whether we just have a placeholder to keep
     * ThreadLocal stack integrity.
     * @return boolean
     */
    public function hasTransaction() {
        return ($this->transactionStatus != null);
    }

    function makeCurrent() {
        // Expose current TransactionStatus, preserving any existing TransactionStatus
        // for restoration after this transaction is complete.
        $this->oldTransactionInfo = Bee_Transactions_Interceptor_TransactionAspectSupport::$transactionInfoHolder;
        Bee_Transactions_Interceptor_TransactionAspectSupport::$transactionInfoHolder = $this;
    }

    function restorePrevious() {
        // Use stack to restore old transaction TransactionInfo.
        // Will be null if none was set.
        Bee_Transactions_Interceptor_TransactionAspectSupport::$transactionInfoHolder = $this->oldTransactionInfo;
    }
}
