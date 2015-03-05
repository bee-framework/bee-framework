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
 * Time: 1:37:25 AM
 * To change this template use File | Settings | File Templates.
 */

class Bee_Transactions_Interceptor_DefaultTransactionAttribute extends Bee_Transactions_Support_DefaultTransactionDefinition implements Bee_Transactions_Interceptor_ITransactionAttribute {

    public function __construct(Bee_Transactions_Interceptor_ITransactionAttribute $other) {
        parent::__construct($other);
    }

    /**
     * Default behavior is as with EJB: rollback on unchecked exception.
     * Additionally attempt to rollback on Error.
     * Consistent with TransactionTemplate's behavior.
     */
    public function rollbackOn(Exception $ex) {
        return ($ex instanceof RuntimeException);
    }
}
