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
 * Time: 3:32:07 AM
 * To change this template use File | Settings | File Templates.
 */

interface Bee_Transactions_ITransactionStatus extends Bee_Transactions_ISavepointManager {

    /**
     * Return whether the present transaction is new (else participating
     * in an existing transaction, or potentially not running in an
     * actual transaction in the first place).
     * @abstract
     * @return boolean
     */
    function isNewTransaction();

    /**
     * Return whether this transaction internally carries a savepoint,
     * that is, has been created as nested transaction based on a savepoint.
     * <p>This method is mainly here for diagnostic purposes, alongside
     * {@link #isNewTransaction()}. For programmatic handling of custom
     * savepoints, use SavepointManager's operations.
     * @see #isNewTransaction()
     * @see #createSavepoint
     * @see #rollbackToSavepoint(Object)
     * @see #releaseSavepoint(Object)
     * @abstract
     * @return boolean
     */
    function hasSavepoint();

    /**
     * Set the transaction rollback-only. This instructs the transaction manager
     * that the only possible outcome of the transaction may be a rollback, as
     * alternative to throwing an exception which would in turn trigger a rollback.
     * <p>This is mainly intended for transactions managed by
     * {@link org.springframework.transaction.support.TransactionTemplate} or
     * {@link org.springframework.transaction.interceptor.TransactionInterceptor},
     * where the actual commit/rollback decision is made by the container.
     * @see org.springframework.transaction.support.TransactionCallback#doInTransaction
     * @see org.springframework.transaction.interceptor.TransactionAttribute#rollbackOn
     */
    function setRollbackOnly();

    /**
     * Return whether the transaction has been marked as rollback-only
     * (either by the application or by the transaction infrastructure).
     * @abstract
     * @return boolean
     */
    function isRollbackOnly();

    /**
     * Return whether this transaction is completed, that is,
     * whether it has already been committed or rolled back.
     * @see PlatformTransactionManager#commit
     * @see PlatformTransactionManager#rollback
     * @abstract
     * @return boolean
     */
    function isCompleted();
}
?>