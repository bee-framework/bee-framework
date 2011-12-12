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
 * Date: Feb 17, 2010
 * Time: 11:34:54 PM
 * To change this template use File | Settings | File Templates.
 */

class Bee_Transactions_Namespace_TxAdviceBeanDefinitionParser extends Bee_Context_Xml_Namespace_AbstractSingleBeanDefinitionParser {

    const ATTRIBUTES = 'attributes';

    const TIMEOUT = 'timeout';

    const READ_ONLY = 'read-only';

    const NAME_MAP = 'nameMap';

    const PROPAGATION = 'propagation';

    const ISOLATION = 'isolation';

    const ROLLBACK_FOR = 'rollback-for';

    const NO_ROLLBACK_FOR = 'no-rollback-for';

    const TRANSACTION_MANAGER_ATTRIBUTE = 'transaction-manager';

    const TRANSACTION_MANAGER_PROPERTY = 'transactionManager';

    const TRANSACTION_ATTRIBUTE_SOURCE = 'transactionAttributeSource';

    const ANNOTATION_TRANSACTION_ATTRIBUTE_SOURCE_CLASS_NAME = 'Bee_Transactions_AnnotationTransactionAttributeSource';

    protected function getBeanClassName(DOMElement $element) {
        return 'Bee_Transactions_Interceptor_TransactionInterceptor';
    }

    protected function doParse(DOMElement $element, Bee_Context_Xml_ParserContext $parserContext, Bee_Context_Support_BeanDefinitionBuilder $builder) {
        // Set the transaction manager property.
        $transactionManagerName = ($element->hasAttribute(self::TRANSACTION_MANAGER_ATTRIBUTE) ?
                $element->getAttribute(self::TRANSACTION_MANAGER_ATTRIBUTE) : "transactionManager");
        $builder->addPropertyReference(self::TRANSACTION_MANAGER_PROPERTY, Bee_Utils_Strings::tokenizeToArray($transactionManagerName, ','));

        $txAttributes = Bee_Utils_Dom::getChildElementsByTagName($element, self::ATTRIBUTES);
        if (count($txAttributes) > 1) {
            $parserContext->getReaderContext()->error(
                    "Element <attributes> is allowed at most once inside element <advice>", $element);
        }
        else if (count($txAttributes) == 1) {
            // Using attributes source.
            $attributeSourceElement = $txAttributes[0];
            $attributeSourceDefinition = $this->parseAttributeSource($attributeSourceElement, $parserContext);
            $builder->addPropertyValue(self::TRANSACTION_ATTRIBUTE_SOURCE, $attributeSourceDefinition);
        }
        else {
            // Assume annotations source.
            // todo: not yet implemented
            $sourceClassName = self::ANNOTATION_TRANSACTION_ATTRIBUTE_SOURCE_CLASS_NAME;
            $srcDef = new Bee_Context_Config_BeanDefinition_Generic();
            $srcDef->setBeanClassName($sourceClassName);
            $builder->addPropertyValue(self::TRANSACTION_ATTRIBUTE_SOURCE, $srcDef);
        }
    }

    private function parseAttributeSource(DOMElement $attrEle, Bee_Context_Xml_ParserContext $parserContext) {
        $methods = Bee_Utils_Dom::getChildElementsByTagName($attrEle, "method");
        $transactionAttributeMap = array();

        foreach ($methods as $methodEle) {

            $name = $methodEle->getAttribute("name");
            $nameHolder = new Bee_Context_Config_TypedStringValue($name);

            $attribute = new Bee_Transactions_Interceptor_RuleBasedTransactionAttribute();
            $propagation = $methodEle->getAttribute(self::PROPAGATION);
            $isolation = $methodEle->getAttribute(self::ISOLATION);
            $timeout = $methodEle->getAttribute(self::TIMEOUT);
            $readOnly = $methodEle->getAttribute(self::READ_ONLY);
            if (Bee_Utils_Strings::hasText($propagation)) {
                $attribute->setPropagationBehavior($propagation);
            }
            if (Bee_Utils_Strings::hasText($isolation)) {
                $attribute->setIsolationLevel($isolation);
            }
            if (Bee_Utils_Strings::hasText($timeout)) {
                $attribute->setTimeout($timeout);
            }
            if (Bee_Utils_Strings::hasText($readOnly)) {
                $attribute->setReadOnly(Bee_Beans_PropertyEditor_Boolean::valueOf($methodEle->getAttribute(self::READ_ONLY)));
            }

            $rollbackRules = array();
            if ($methodEle->hasAttribute(self::ROLLBACK_FOR)) {
                $rollbackForValue = $methodEle->getAttribute(self::ROLLBACK_FOR);
                $this->addRollbackRuleAttributesTo($rollbackRules, $rollbackForValue);
            }
            if ($methodEle->hasAttribute(self::NO_ROLLBACK_FOR)) {
                $noRollbackForValue = $methodEle->getAttribute(self::NO_ROLLBACK_FOR);
                $this->addNoRollbackRuleAttributesTo($rollbackRules, $noRollbackForValue);
            }
            $attribute->setRollbackRules($rollbackRules);

            $transactionAttributeMap[$nameHolder] = $attribute;
        }

        $attributeSourceDefinition = new Bee_Context_Config_BeanDefinition_Generic();
        $attributeSourceDefinition->setBeanClassName('Bee_Transactions_NameMatchTransactionAttributeSource');
        $attributeSourceDefinition->addPropertyValue(new Bee_Beans_PropertyValue(self::NAME_MAP, $transactionAttributeMap));
        return $attributeSourceDefinition;
    }

    private function addRollbackRuleAttributesTo(array &$rollbackRules, $rollbackForValue) {
        $exceptionTypeNames = Bee_Utils_Strings::tokenizeToArray($rollbackForValue, ',');
        foreach($exceptionTypeNames as $exceptionTypeName) {
            $rollbackRules[] = new Bee_Transactions_Interceptor_RollbackRuleAttribute($exceptionTypeName);
        }
    }

    private function addNoRollbackRuleAttributesTo(array &$rollbackRules, $noRollbackForValue) {
        $exceptionTypeNames = Bee_Utils_Strings::tokenizeToArray($noRollbackForValue, ',');
        foreach($exceptionTypeNames as $exceptionTypeName) {
            $rollbackRules[] = new Bee_Transactions_Interceptor_NoRollbackRuleAttribute($exceptionTypeName);
        }
    }
}
?>