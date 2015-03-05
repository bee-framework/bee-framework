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
use Bee\Beans\PropertyEditor\BooleanPropertyEditor;
use Bee\Beans\PropertyValue;
use Bee\Context\Config\BeanDefinition\GenericBeanDefinition;
use Bee\Context\Support\BeanDefinitionBuilder;
use Bee\Context\Xml\ParserContext;
use Bee\Context\Xml\XmlNamespace\AbstractSingleBeanDefinitionParser;
use Bee\Utils\Dom;
use Bee\Utils\Strings;

/**
 * Created by IntelliJ IDEA.
 * User: mp
 * Date: Feb 17, 2010
 * Time: 11:34:54 PM
 * To change this template use File | Settings | File Templates.
 */

class Bee_Transactions_Namespace_TxAdviceBeanDefinitionParser extends AbstractSingleBeanDefinitionParser {

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

	/**
	 * @param DOMElement $element
	 * @param ParserContext $parserContext
	 * @param BeanDefinitionBuilder $builder
	 * @throws \Bee\Context\BeanCreationException
	 */
    protected function doParse(DOMElement $element, ParserContext $parserContext, BeanDefinitionBuilder $builder) {
        // Set the transaction manager property.
        $transactionManagerName = ($element->hasAttribute(self::TRANSACTION_MANAGER_ATTRIBUTE) ?
                $element->getAttribute(self::TRANSACTION_MANAGER_ATTRIBUTE) : "transactionManager");
        $builder->addPropertyReference(self::TRANSACTION_MANAGER_PROPERTY, Strings::tokenizeToArray($transactionManagerName, ','));

        $txAttributes = Dom::getChildElementsByTagName($element, self::ATTRIBUTES);
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
            $srcDef = new GenericBeanDefinition();
            $srcDef->setBeanClassName($sourceClassName);
            $builder->addPropertyValue(self::TRANSACTION_ATTRIBUTE_SOURCE, $srcDef);
        }
    }

	/**
	 * @param DOMElement $attrEle
	 * @param ParserContext $parserContext
	 * @return GenericBeanDefinition
	 */
	private function parseAttributeSource(DOMElement $attrEle, ParserContext $parserContext) {
        $methods = Dom::getChildElementsByTagName($attrEle, "method");
        $transactionAttributeMap = array();

        foreach ($methods as $methodEle) {

            $name = $methodEle->getAttribute("name");
//            $nameHolder = new TypedStringValue($name);

            $attribute = new Bee_Transactions_Interceptor_RuleBasedTransactionAttribute();
            $propagation = $methodEle->getAttribute(self::PROPAGATION);
            $isolation = $methodEle->getAttribute(self::ISOLATION);
            $timeout = $methodEle->getAttribute(self::TIMEOUT);
            $readOnly = $methodEle->getAttribute(self::READ_ONLY);
            if (Strings::hasText($propagation)) {
                $attribute->setPropagationBehavior($propagation);
            }
            if (Strings::hasText($isolation)) {
                $attribute->setIsolationLevel($isolation);
            }
            if (Strings::hasText($timeout)) {
                $attribute->setTimeout($timeout);
            }
            if (Strings::hasText($readOnly)) {
                $attribute->setReadOnly(BooleanPropertyEditor::valueOf($methodEle->getAttribute(self::READ_ONLY)));
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

//            $transactionAttributeMap[$nameHolder] = $attribute;
            $transactionAttributeMap[$name] = $attribute;
        }

        $attributeSourceDefinition = new GenericBeanDefinition();
        $attributeSourceDefinition->setBeanClassName('Bee_Transactions_NameMatchTransactionAttributeSource');
        $attributeSourceDefinition->addPropertyValue(new PropertyValue(self::NAME_MAP, $transactionAttributeMap));
        return $attributeSourceDefinition;
    }

	/**
	 * @param array $rollbackRules
	 * @param $rollbackForValue
	 */
    private function addRollbackRuleAttributesTo(array &$rollbackRules, $rollbackForValue) {
        $exceptionTypeNames = Strings::tokenizeToArray($rollbackForValue, ',');
        foreach($exceptionTypeNames as $exceptionTypeName) {
            $rollbackRules[] = new Bee_Transactions_Interceptor_RollbackRuleAttribute($exceptionTypeName);
        }
    }

	/**
	 * @param array $rollbackRules
	 * @param $noRollbackForValue
	 */
    private function addNoRollbackRuleAttributesTo(array &$rollbackRules, $noRollbackForValue) {
        $exceptionTypeNames = Strings::tokenizeToArray($noRollbackForValue, ',');
        foreach($exceptionTypeNames as $exceptionTypeName) {
            $rollbackRules[] = new Bee_Transactions_Interceptor_NoRollbackRuleAttribute($exceptionTypeName);
        }
    }
}