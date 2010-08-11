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
 * Time: 1:44:44 AM
 * To change this template use File | Settings | File Templates.
 */

class Bee_Transactions_Interceptor_RuleBasedTransactionAttribute extends Bee_Transactions_Interceptor_DefaultTransactionAttribute {

    /** Prefix for rollback-on-exception rules in description strings */
    const PREFIX_ROLLBACK_RULE = "-";

    /** Prefix for commit-on-exception rules in description strings */
    const PREFIX_COMMIT_RULE = "+";

    /**
     * @var Bee_Transactions_Interceptor_RollbackRuleAttribute[]
     */
    private $rollbackRules;


    public function __construct(Bee_Transactions_Interceptor_RuleBasedTransactionAttribute $other = null) {
        parent::__construct($other);
        $this->rollbackRules = $other->rollbackRules;
    }

    /**
     * Set the list of <code>RollbackRuleAttribute</code> objects
     * (and/or <code>NoRollbackRuleAttribute</code> objects) to apply.
     * @param Bee_Transactions_Interceptor_RollbackRuleAttribute[] $rollbackRules
     * @return void
     */
    public function setRollbackRules(array $rollbackRules) {
        $this->rollbackRules = $rollbackRules;
    }

    /**
     * Return the list of <code>RollbackRuleAttribute</code> objects (never <code>null</code>).
     * @return Bee_Transactions_Interceptor_RollbackRuleAttribute[]
     */
    public function getRollbackRules() {
        if ($this->rollbackRules == null) {
            $this->rollbackRules = array();
        }
        return $this->rollbackRules;
    }


    /**
     * Winning rule is the shallowest rule (that is, the closest in the
     * inheritance hierarchy to the exception). If no rule applies (-1),
     * return false.
     * @see TransactionAttribute#rollbackOn(java.lang.Throwable)
     */
    public function rollbackOn(Exception $ex) {
        $winner = null;
        $deepest = 100000;

        if ($this->rollbackRules != null) {
            foreach($this->rollbackRules as $rule) {
                $depth = $rule->getDepth($ex);
                if ($depth >= 0 && $depth < $deepest) {
                    $deepest = $depth;
                    $winner = $rule;
                }
            }
        }

        // User superclass behavior (rollback on unchecked) if no rule matches.
        if ($winner == null) {
            return parent::rollbackOn($ex);
        }

        return !($winner instanceof Bee_Transactions_Interceptor_NoRollbackRuleAttribute);
    }
}
