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
 * Time: 1:24:54 AM
 * To change this template use File | Settings | File Templates.
 */

class Bee_Transactions_Support_DefaultTransactionDefinition implements Bee_Transactions_ITransactionDefinition {

    /** Prefix for the propagation constants defined in TransactionDefinition */
    const PREFIX_PROPAGATION = "PROPAGATION_";

    /** Prefix for the isolation constants defined in TransactionDefinition */
    const PREFIX_ISOLATION = "ISOLATION_";

    /** Prefix for transaction timeout values in description strings */
    const PREFIX_TIMEOUT = "timeout_";

    /** Marker for read-only transactions in description strings */
    const READ_ONLY_MARKER = "readOnly";


    /** Constants instance for TransactionDefinition */
//    static final Constants constants = new Constants(TransactionDefinition.class);

    private $propagationBehavior = self::PROPAGATION_REQUIRED;

    private $isolationLevel = self::ISOLATION_DEFAULT;

    private $timeout = self::TIMEOUT_DEFAULT;

    private $readOnly = false;

    private $name;


    public function __construct(Bee_Transactions_Support_DefaultTransactionDefinition $other) {
        $this->propagationBehavior = $other->getPropagationBehavior();
        $this->isolationLevel = $other->getIsolationLevel();
        $this->timeout = $other->getTimeout();
        $this->readOnly = $other->isReadOnly();
        $this->name = $other->getName();
    }

    /**
     * Set the propagation behavior by the name of the corresponding constant in
     * TransactionDefinition, e.g. "PROPAGATION_REQUIRED".
     * @param constantName name of the constant
     * @exception IllegalArgumentException if the supplied value is not resolvable
     * to one of the <code>PROPAGATION_</code> constants or is <code>null</code>
     * @see #setPropagationBehavior
     * @see #PROPAGATION_REQUIRED
     */
//    public final void setPropagationBehaviorName($constantName) {
//        if (constantName == null || !constantName.startsWith(PREFIX_PROPAGATION)) {
//            throw new IllegalArgumentException("Only propagation constants allowed");
//        }
//        setPropagationBehavior(constants.asNumber(constantName).intValue());
//    }

    /**
     * Set the propagation behavior. Must be one of the propagation constants
     * in the TransactionDefinition interface. Default is PROPAGATION_REQUIRED.
     * @exception IllegalArgumentException if the supplied value is not
     * one of the <code>PROPAGATION_</code> constants
     * @see #PROPAGATION_REQUIRED
     */
    public final function setPropagationBehavior($propagationBehavior) {
        $this->propagationBehavior = $propagationBehavior;
    }

    public final function getPropagationBehavior() {
        return $this->propagationBehavior;
    }

    /**
     * Set the isolation level by the name of the corresponding constant in
     * TransactionDefinition, e.g. "ISOLATION_DEFAULT".
     * @param constantName name of the constant
     * @exception IllegalArgumentException if the supplied value is not resolvable
     * to one of the <code>ISOLATION_</code> constants or is <code>null</code>
     * @see #setIsolationLevel
     * @see #ISOLATION_DEFAULT
     */
//    public final void setIsolationLevelName(String constantName) throws IllegalArgumentException {
//        if (constantName == null || !constantName.startsWith(PREFIX_ISOLATION)) {
//            throw new IllegalArgumentException("Only isolation constants allowed");
//        }
//        setIsolationLevel(constants.asNumber(constantName).intValue());
//    }

    /**
     * Set the isolation level. Must be one of the isolation constants
     * in the TransactionDefinition interface. Default is ISOLATION_DEFAULT.
     * @exception IllegalArgumentException if the supplied value is not
     * one of the <code>ISOLATION_</code> constants
     * @see #ISOLATION_DEFAULT
     */
    public final function setIsolationLevel($isolationLevel) {
        $this->isolationLevel = $isolationLevel;
    }

    public final function getIsolationLevel() {
        return $this->isolationLevel;
    }

    /**
     * Set the timeout to apply, as number of seconds.
     * Default is TIMEOUT_DEFAULT (-1).
     * @see #TIMEOUT_DEFAULT
     */
    public final function setTimeout($timeout) {
        $this->timeout = $timeout;
    }

    public final function getTimeout() {
        return $this->timeout;
    }

    /**
     * Set whether to optimize as read-only transaction.
     * Default is "false".
     */
    public final function setReadOnly($readOnly) {
        $this->readOnly = $readOnly;
    }

    public final function isReadOnly() {
        return $this->readOnly;
    }

    /**
     * Set the name of this transaction. Default is none.
     * <p>This will be used as transaction name to be shown in a
     * transaction monitor, if applicable (for example, WebLogic's).
     */
    public final function setName($name) {
        $this->name = $name;
    }

    public final function getName() {
        return $this->name;
    }
}
