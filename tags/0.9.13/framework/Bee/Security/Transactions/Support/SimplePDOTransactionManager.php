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
 * Time: 6:14:45 AM
 * To change this template use File | Settings | File Templates.
 */

class Bee_Transactions_Support_SimplePDOTransactionManager implements Bee_Transactions_IPlatformTransactionManager {

    /**
     * @var PDO
     */
    private $connection;

    public function getTransaction(Bee_Transactions_ITransactionDefinition $definition) {
        // todo
    }

    /**
     * Commit the given transaction, with regard to its status. If the transaction
     * has been marked rollback-only programmatically, perform a rollback.
     * <p>If the transaction wasn't a new one, omit the commit for proper
     * participation in the surrounding transaction. If a previous transaction
     * has been suspended to be able to create a new one, resume the previous
     * transaction after committing the new one.
     * <p>Note that when the commit call completes, no matter if normally or
     * throwing an exception, the transaction must be fully completed and
     * cleaned up. No rollback call should be expected in such a case.
     * <p>If this method throws an exception other than a TransactionException,
     * then some before-commit error caused the commit attempt to fail. For
     * example, an O/R Mapping tool might have tried to flush changes to the
     * database right before commit, with the resulting DataAccessException
     * causing the transaction to fail. The original exception will be
     * propagated to the caller of this commit method in such a case.
     * @param Bee_Transactions_ITransactionStatus $status object returned by the <code>getTransaction</code> method
     * @throws UnexpectedRollbackException in case of an unexpected rollback
     * that the transaction coordinator initiated
     * @throws HeuristicCompletionException in case of a transaction failure
     * caused by a heuristic decision on the side of the transaction coordinator
     * @throws TransactionSystemException in case of commit or system errors
     * (typically caused by fundamental resource failures)
     * @throws IllegalTransactionStateException if the given transaction
     * is already completed (that is, committed or rolled back)
     * @see TransactionStatus#setRollbackOnly
     */
    public function commit(Bee_Transactions_ITransactionStatus $status) {
        // todo
    }

    /**
     * Perform a rollback of the given transaction.
     * <p>If the transaction wasn't a new one, just set it rollback-only for proper
     * participation in the surrounding transaction. If a previous transaction
     * has been suspended to be able to create a new one, resume the previous
     * transaction after rolling back the new one.
     * <p><b>Do not call rollback on a transaction if commit threw an exception.</b>
     * The transaction will already have been completed and cleaned up when commit
     * returns, even in case of a commit exception. Consequently, a rollback call
     * after commit failure will lead to an IllegalTransactionStateException.
     * @param Bee_Transactions_ITransactionStatus $status object returned by the <code>getTransaction</code> method
     * @throws TransactionSystemException in case of rollback or system errors
     * (typically caused by fundamental resource failures)
     * @throws IllegalTransactionStateException if the given transaction
     * is already completed (that is, committed or rolled back)
     */
    public function rollback(Bee_Transactions_ITransactionStatus $status) {
        // todo
    }

}
?>