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
 * Time: 5:57:27 AM
 * To change this template use File | Settings | File Templates.
 */

interface Bee_Transactions_Support_ITransactionSynchronization {

    /** Completion status in case of proper commit */
    const STATUS_COMMITTED = 0;

    /** Completion status in case of proper rollback */
    const STATUS_ROLLED_BACK = 1;

    /** Completion status in case of heuristic mixed completion or system errors */
    const STATUS_UNKNOWN = 2;


    /**
     * Suspend this synchronization.
     * Supposed to unbind resources from TransactionSynchronizationManager if managing any.
     * @see TransactionSynchronizationManager#unbindResource
     */
    function suspend();

    /**
     * Resume this synchronization.
     * Supposed to rebind resources to TransactionSynchronizationManager if managing any.
     * @see TransactionSynchronizationManager#bindResource
     */
    function resume();

    /**
     * Invoked before transaction commit (before "beforeCompletion").
     * Can e.g. flush transactional O/R Mapping sessions to the database.
     * <p>This callback does <i>not</i> mean that the transaction will actually be committed.
     * A rollback decision can still occur after this method has been called. This callback
     * is rather meant to perform work that's only relevant if a commit still has a chance
     * to happen, such as flushing SQL statements to the database.
     * <p>Note that exceptions will get propagated to the commit caller and cause a
     * rollback of the transaction.
     * @param readOnly whether the transaction is defined as read-only transaction
     * @throws RuntimeException in case of errors; will be <b>propagated to the caller</b>
     * (note: do not throw TransactionException subclasses here!)
     * @see #beforeCompletion
     */
    function beforeCommit($readOnly);

    /**
     * Invoked before transaction commit/rollback.
     * Can perform resource cleanup <i>before</i> transaction completion.
     * <p>This method will be invoked after <code>beforeCommit</code>, even when
     * <code>beforeCommit</code> threw an exception. This callback allows for
     * closing resources before transaction completion, for any outcome.
     * @throws RuntimeException in case of errors; will be <b>logged but not propagated</b>
     * (note: do not throw TransactionException subclasses here!)
     * @see #beforeCommit
     * @see #afterCompletion
     */
    function beforeCompletion();

    /**
     * Invoked after transaction commit. Can perform further operations right
     * <i>after</i> the main transaction has <i>successfully</i> committed.
     * <p>Can e.g. commit further operations that are supposed to follow on a successful
     * commit of the main transaction, like confirmation messages or emails.
     * <p><b>NOTE:</b> The transaction will have been committed already, but the
     * transactional resources might still be active and accessible. As a consequence,
     * any data access code triggered at this point will still "participate" in the
     * original transaction, allowing to perform some cleanup (with no commit following
     * anymore!), unless it explicitly declares that it needs to run in a separate
     * transaction. Hence: <b>Use <code>PROPAGATION_REQUIRES_NEW</code> for any
     * transactional operation that is called from here.</b>
     * @throws RuntimeException in case of errors; will be <b>propagated to the caller</b>
     * (note: do not throw TransactionException subclasses here!)
     */
    function afterCommit();

    /**
     * Invoked after transaction commit/rollback.
     * Can perform resource cleanup <i>after</i> transaction completion.
     * <p><b>NOTE:</b> The transaction will have been committed or rolled back already,
     * but the transactional resources might still be active and accessible. As a
     * consequence, any data access code triggered at this point will still "participate"
     * in the original transaction, allowing to perform some cleanup (with no commit
     * following anymore!), unless it explicitly declares that it needs to run in a
     * separate transaction. Hence: <b>Use <code>PROPAGATION_REQUIRES_NEW</code>
     * for any transactional operation that is called from here.</b>
     * @param status completion status according to the <code>STATUS_*</code> constants
     * @throws RuntimeException in case of errors; will be <b>logged but not propagated</b>
     * (note: do not throw TransactionException subclasses here!)
     * @see #STATUS_COMMITTED
     * @see #STATUS_ROLLED_BACK
     * @see #STATUS_UNKNOWN
     * @see #beforeCompletion
     */
    function afterCompletion($status);
}
