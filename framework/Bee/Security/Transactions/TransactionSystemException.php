<?php
/*
 * Copyright 2008-2014 the original author or authors.
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
 * Time: 3:55:21 AM
 * To change this template use File | Settings | File Templates.
 */

class Bee_Transactions_TransactionSystemException extends Exception {

    /**
     * @var Exception
     */
    private $applicationException;

    public function __construct($message, Exception $cause = null) {
        parent::__construct($message, 0, $cause);
    }

    /**
     * Set an application exception that was thrown before this transaction exception,
     * preserving the original exception despite the overriding TransactionSystemException.
     * @param Exception $ex the application exception
     * @throws IllegalStateException if this TransactionSystemException already holds an
     * application exception
     */
    public function initApplicationException(Exception $ex) {
        if ($this->applicationException != null) {
            throw new Exception("Already holding an application exception: " + $this->applicationException);
        }
        $this->applicationException = $ex;
    }

    /**
     * Return the application exception that was thrown before this transaction exception,
     * if any.
     * @return Exception the application exception, or <code>null</code> if none set
     */
    public final function getApplicationException() {
        return $this->applicationException;
    }
}