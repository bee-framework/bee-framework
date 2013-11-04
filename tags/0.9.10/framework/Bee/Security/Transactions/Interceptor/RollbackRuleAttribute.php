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
 * Time: 1:48:34 AM
 * To change this template use File | Settings | File Templates.
 */

class Bee_Transactions_Interceptor_RollbackRuleAttribute {

    /**
     * The {@link RollbackRuleAttribute rollback rule} for
     * {@link RuntimeException RuntimeExceptions}.
     */
    public static function ROLLBACK_ON_RUNTIME_EXCEPTIONS() {
        return new Bee_Transactions_Interceptor_RollbackRuleAttribute('RuntimeException');
    }


    /**
     * Could hold exception, resolving class name but would always require FQN.
     * This way does multiple string comparisons, but how often do we decide
     * whether to roll back a transaction following an exception?
     */
    private $exceptionName;


    /**
     * Create a new instance of the <code>RollbackRuleAttribute</code> class.
     * <p>This is the preferred way to construct a rollback rule that matches
     * the supplied {@link Exception} class (and subclasses).
     * @param clazz throwable class; must be {@link Throwable} or a subclass
     * of <code>Throwable</code>
     * @throws IllegalArgumentException if the supplied <code>clazz</code> is
     * not a <code>Throwable</code> type or is <code>null</code>
     */
    public function __construct($className) {
        if (Bee_Utils_Types::isAssignable($className, 'Exception')) {
            throw new InvalidArgumentException(
                    "Cannot construct rollback rule from [$className]: it's not an Exception");
        }
        $this->exceptionName = $className;
    }

    /**
     * Return the pattern for the exception name.
     */
    public function getExceptionName() {
        return $this->exceptionName;
    }

    /**
     * Return the depth of the superclass matching.
     * <p><code>0</code> means <code>ex</code> matches exactly. Returns
     * <code>-1</code> if there is no match. Otherwise, returns depth with the
     * lowest depth winning.
     */
    public function getDepth(Exception $ex) {
        return $this->doGetDepth(get_class($ex), 0);
    }


    private function doGetDepth($exceptionClassName, $depth) {
        if ($exceptionClassName == $this->exceptionName) {
            // Found it!
            return $depth;
        }
        // If we've gone as far as we can go and haven't found it...
        if ($exceptionClassName == 'Exception') {
            return -1;
        }
        return $this->getDepth(get_parent_class($exceptionClassName), $depth + 1);
    }

    public function __toString() {
        return "RollbackRuleAttribute with pattern [$this->exceptionName]";
    }
}
?>