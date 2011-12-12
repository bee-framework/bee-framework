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
 * Time: 5:24:00 AM
 * To change this template use File | Settings | File Templates.
 */

class Bee_Transactions_Support_DefaultTransactionStatus extends Bee_Transactions_Support_AbstractTransactionStatus {

    /**
     * @var mixed
     */
    private $transaction;

    /**
     * @var boolean
     */
    private $newTransaction;

    /**
     * @var boolean
     */
    private $newSynchronization;

    /**
     * @var boolean
     */
    private $readOnly;

    /**
     * @var boolean
     */
    private $debug;

    /**
     * @var mixed
     */
    private $suspendedResources;


    /**
     * Create a new DefaultTransactionStatus instance.
     * @param mixed $transaction underlying transaction object that can hold
     * state for the internal transaction implementation
     * @param boolean $newTransaction if the transaction is new,
     * else participating in an existing transaction
     * @param boolean $newSynchronization if a new transaction synchronization
     * has been opened for the given transaction
     * @param boolean $readOnly whether the transaction is read-only
     * @param boolean $debug should debug logging be enabled for the handling of this transaction?
     * Caching it in here can prevent repeated calls to ask the logging system whether
     * debug logging should be enabled.
     * @param mixed $suspendedResources a holder for resources that have been suspended
     * for this transaction, if any
     */
    public function __construct($transaction, $newTransaction, $newSynchronization, $readOnly, $debug, $suspendedResources) {
        $this->transaction = $transaction;
        $this->newTransaction = $newTransaction;
        $this->newSynchronization = $newSynchronization;
        $this->readOnly = $readOnly;
        $this->debug = $debug;
        $this->suspendedResources = $suspendedResources;
    }

    /**
     * Return the underlying transaction object.
     * @return mixed
     */
    public function getTransaction() {
        return $this->transaction;
    }

    /**
     * Return whether there is an actual transaction active.
     * @return boolean
     */
    public function hasTransaction() {
        return ($this->transaction != null);
    }

    /**
     * @return boolean
     */
    public function isNewTransaction() {
        return ($this->hasTransaction() && $this->newTransaction);
    }

    /**
     * Return if a new transaction synchronization has been opened
     * for this transaction.
     * @return boolean
     */
    public function isNewSynchronization() {
        return $this->newSynchronization;
    }

    /**
     * @return Return if this transaction is defined as read-only transaction.
     */
    public function isReadOnly() {
        return $this->readOnly;
    }

    /**
     * Return whether the progress of this transaction is debugged. This is used
     * by AbstractPlatformTransactionManager as an optimization, to prevent repeated
     * calls to logger.isDebug(). Not really intended for client code.
     * @return boolean
     */
    public function isDebug() {
        return $this->debug;
    }

    /**
     * Return the holder for resources that have been suspended for this transaction,
     * if any.
     */
    public function getSuspendedResources() {
        return $this->suspendedResources;
    }


    //---------------------------------------------------------------------
    // Enable functionality through underlying transaction object
    //---------------------------------------------------------------------

    /**
     * Determine the rollback-only flag via checking both the transaction object,
     * provided that the latter implements the SmartTransactionObject interface.
     * <p>Will return "true" if the transaction itself has been marked rollback-only
     * by the transaction coordinator, for example in case of a timeout.
     * @see SmartTransactionObject#isRollbackOnly
     */
    public function isGlobalRollbackOnly() {
        return ($this->transaction instanceof Bee_Transactions_Support_ISmartTransactionObject) && $this->transaction->isRollbackOnly();
    }

    /**
     * This implementation exposes the SavepointManager interface
     * of the underlying transaction object, if any.
     */
    protected function getSavepointManager() {
        if (!$this->isTransactionSavepointManager()) {
            throw new Bee_Transactions_NestedTransactionNotSupportedException(
                "Transaction object [{$this->getTransaction()}] does not support savepoints");
        }
        return $this->getTransaction();
    }

    /**
     * Return whether the underlying transaction implements the
     * SavepointManager interface.
     * @see #getTransaction
     * @see org.springframework.transaction.SavepointManager
     */
    public function isTransactionSavepointManager() {
        return ($this->getTransaction() instanceof Bee_Transactions_ISavepointManager);
    }

}
?>