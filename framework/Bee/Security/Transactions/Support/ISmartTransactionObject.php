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
 * Time: 5:31:43 AM
 * To change this template use File | Settings | File Templates.
 */

interface Bee_Transactions_Support_ISmartTransactionObject {

    /**
     * Return whether the transaction is internally marked as rollback-only.
     * Can, for example, check the JTA UserTransaction.
     * @see javax.transaction.UserTransaction#getStatus
     * @see javax.transaction.Status#STATUS_MARKED_ROLLBACK
     */
    function isRollbackOnly();

}
