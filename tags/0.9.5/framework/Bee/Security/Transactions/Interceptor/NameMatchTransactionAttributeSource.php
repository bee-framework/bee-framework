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
 * Time: 2:41:04 AM
 * To change this template use File | Settings | File Templates.
 */

class Bee_Transactions_Interceptor_NameMatchTransactionAttributeSource implements Bee_Transactions_Interceptor_ITransactionAttributeSource {

    /**
     * Keys are method names; values are TransactionAttributes
     * @var Bee_Transactions_Interceptor_ITransactionAttribute[]
     */
    private $nameMap = array();


    /**
     * Set a name/attribute map, consisting of method names
     * (e.g. "myMethod") and TransactionAttribute instances
     * (or Strings to be converted to TransactionAttribute instances).
     * @see TransactionAttribute
     * @see TransactionAttributeEditor
     */
    public function setNameMap(array $nameMap) {
        foreach($nameMap as $name => $value) {
            // Check whether we need to convert from String to TransactionAttribute.
            if (!($value instanceof Bee_Transactions_Interceptor_ITransactionAttribute)) {
                throw new Exception("NOT YET IMPLEMENTED");
//                $editor = new TransactionAttributeEditor();
//                $editor->setAsText($value);
//                $attr = $editor->getValue();
            }

            $this->addTransactionalMethod($name, $value);
        }
    }

    /**
     * Add an attribute for a transactional method.
     * <p>Method names can be exact matches, or of the pattern "xxx*",
     * "*xxx" or "*xxx*" for matching multiple methods.
     * @param string $methodName the name of the method
     * @param Bee_Transactions_Interceptor_ITransactionAttribute $attr attribute associated with the method
     */
    public function addTransactionalMethod($methodName, Bee_Transactions_Interceptor_ITransactionAttribute $attr) {
        $this->nameMap[$methodName] = $attr;
    }


    public function getTransactionAttribute(ReflectionMethod $method, $targetClassName) {
        // look for direct name match
        $methodName = $method->getName();
        $attr = $this->nameMap[$methodName];

        if ($attr == null) {
            // Look for most specific name match.
            $bestNameMatch = null;
            foreach($this->nameMap as $mappedName => $attrCand) {
                if ($this->isMatch($methodName, $mappedName) &&
                        ($bestNameMatch == null || strlen($bestNameMatch) <= strlen($mappedName))) {
                    $attr = $attrCand;
                    $bestNameMatch = $mappedName;
                }
            }
        }

        return $attr;
    }

    /**
     * Return if the given method name matches the mapped name.
     * <p>The default implementation checks for "xxx*", "*xxx" and "*xxx*" matches,
     * as well as direct equality. Can be overridden in subclasses.
     * @param string $methodName the method name of the class
     * @param string $mappedName the name in the descriptor
     * @return if the names match
     * @see org.springframework.util.PatternMatchUtils#simpleMatch(String, String)
     */
    protected function isMatch($methodName, $mappedName) {
        return Bee_Utils_PatternMatcher::simpleMatch($mappedName, $methodName);
    }
}
?>