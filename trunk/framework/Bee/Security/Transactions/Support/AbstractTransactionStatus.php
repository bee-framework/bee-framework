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
 * Time: 5:16:49 AM
 * To change this template use File | Settings | File Templates.
 */
abstract class Bee_Transactions_Support_AbstractTransactionStatus implements Bee_Transactions_ITransactionStatus {

    private $rollbackOnly = false;

    private $completed = false;

    private $savepoint;


    //---------------------------------------------------------------------
    // Handling of current transaction state
    //---------------------------------------------------------------------

    public function setRollbackOnly() {
        $this->rollbackOnly = true;
    }

    /**
     * Determine the rollback-only flag via checking both the local rollback-only flag
     * of this TransactionStatus and the global rollback-only flag of the underlying
     * transaction, if any.
     * @see #isLocalRollbackOnly()
     * @see #isGlobalRollbackOnly()
     */
    public function isRollbackOnly() {
        return ($this->isLocalRollbackOnly() || $this->isGlobalRollbackOnly());
    }

    /**
     * Determine the rollback-only flag via checking this TransactionStatus.
     * <p>Will only return "true" if the application called <code>setRollbackOnly</code>
     * on this TransactionStatus object.
     */
    public function isLocalRollbackOnly() {
        return $this->rollbackOnly;
    }

    /**
     * Template method for determining the global rollback-only flag of the
     * underlying transaction, if any.
     * <p>This implementation always returns <code>false</code>.
     */
    public function isGlobalRollbackOnly() {
        return false;
    }

    /**
     * Mark this transaction as completed, that is, committed or rolled back.
     */
    public function setCompleted() {
        $this->completed = true;
    }

    public function isCompleted() {
        return $this->completed;
    }


    //---------------------------------------------------------------------
    // Handling of current savepoint state
    //---------------------------------------------------------------------

    /**
     * Set a savepoint for this transaction. Useful for PROPAGATION_NESTED.
     * @see org.springframework.transaction.TransactionDefinition#PROPAGATION_NESTED
     */
    protected function setSavepoint($savepoint) {
        $this->savepoint = $savepoint;
    }

    /**
     * Get the savepoint for this transaction, if any.
     */
    protected function getSavepoint() {
        return $this->savepoint;
    }

    public function hasSavepoint() {
        return ($this->savepoint != null);
    }

    /**
     * Create a savepoint and hold it for the transaction.
     * @throws org.springframework.transaction.NestedTransactionNotSupportedException
     * if the underlying transaction does not support savepoints
     */
    public function createAndHoldSavepoint() {
        $this->setSavepoint($this->getSavepointManager()->createSavepoint());
    }

    /**
     * Roll back to the savepoint that is held for the transaction.
     */
    public function rollbackToHeldSavepoint() {
        if (!$this->hasSavepoint()) {
            throw new Bee_Transactions_TransactionUsageException("No savepoint associated with current transaction");
        }
        $this->getSavepointManager()->rollbackToSavepoint($this->getSavepoint());
        $this->setSavepoint(null);
    }

    /**
     * Release the savepoint that is held for the transaction.
     */
    public function releaseHeldSavepoint() {
        if (!$this->hasSavepoint()) {
            throw new Bee_Transactions_TransactionUsageException("No savepoint associated with current transaction");
        }
        $this->getSavepointManager()->releaseSavepoint($this->getSavepoint());
        $this->setSavepoint(null);
    }


    //---------------------------------------------------------------------
    // Implementation of SavepointManager
    //---------------------------------------------------------------------

    /**
     * This implementation delegates to a SavepointManager for the
     * underlying transaction, if possible.
     * @see #getSavepointManager()
     * @see org.springframework.transaction.SavepointManager
     */
    public function createSavepoint() {
        return $this->getSavepointManager()->createSavepoint();
    }

    /**
     * This implementation delegates to a SavepointManager for the
     * underlying transaction, if possible.
     * @throws org.springframework.transaction.NestedTransactionNotSupportedException
     * @see #getSavepointManager()
     * @see org.springframework.transaction.SavepointManager
     */
    public function rollbackToSavepoint($savepoint) {
        $this->getSavepointManager()->rollbackToSavepoint($savepoint);
    }

    /**
     * This implementation delegates to a SavepointManager for the
     * underlying transaction, if possible.
     * @see #getSavepointManager()
     * @see org.springframework.transaction.SavepointManager
     */
    public function releaseSavepoint($savepoint) {
        $this->getSavepointManager()->releaseSavepoint($savepoint);
    }

    /**
     * Return a SavepointManager for the underlying transaction, if possible.
     * <p>Default implementation always throws a NestedTransactionNotSupportedException.
     * @throws org.springframework.transaction.NestedTransactionNotSupportedException
     * if the underlying transaction does not support savepoints
     */
    /**
     * @access protected
     * @throws NestedTransactionNotSupportedException
     * @return Bee_Transactions_ISavepointManager
     */
    protected function getSavepointManager() {
        throw new Bee_Transactions_NestedTransactionNotSupportedException("This transaction does not support savepoints");
    }
}
