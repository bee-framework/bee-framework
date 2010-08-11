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
 * Time: 3:32:53 AM
 * To change this template use File | Settings | File Templates.
 */

interface Bee_Transactions_ISavepointManager {
    /**
     * Create a new savepoint. You can roll back to a specific savepoint
     * via <code>rollbackToSavepoint</code>, and explicitly release a
     * savepoint that you don't need anymore via <code>releaseSavepoint</code>.
     * <p>Note that most transaction managers will automatically release
     * savepoints at transaction completion.
     * @return mixed a savepoint object, to be passed into rollbackToSavepoint
     * or releaseSavepoint
     * @throws NestedTransactionNotSupportedException if the underlying
     * transaction does not support savepoints
     * @throws TransactionException if the savepoint could not be created,
     * for example because the transaction is not in an appropriate state
     * @see java.sql.Connection#setSavepoint
     */
    function createSavepoint();

    /**
     * Roll back to the given savepoint. The savepoint will be
     * automatically released afterwards.
     * @param mixed $savepoint the savepoint to roll back to
     * @throws NestedTransactionNotSupportedException if the underlying
     * transaction does not support savepoints
     * @throws TransactionException if the rollback failed
     * @see java.sql.Connection#rollback(java.sql.Savepoint)
     */
    function rollbackToSavepoint($savepoint);

    /**
     * Explicitly release the given savepoint.
     * <p>Note that most transaction managers will automatically release
     * savepoints at transaction completion.
     * <p>Implementations should fail as silently as possible if
     * proper resource cleanup will still happen at transaction completion.
     * @param mixed $savepoint the savepoint to release
     * @throws NestedTransactionNotSupportedException if the underlying
     * transaction does not support savepoints
     * @throws TransactionException if the release failed
     * @see java.sql.Connection#releaseSavepoint
     */
    function releaseSavepoint($savepoint);

}
