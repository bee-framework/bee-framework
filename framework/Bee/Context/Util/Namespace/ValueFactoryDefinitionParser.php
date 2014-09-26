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
use Bee\Context\Support\BeanDefinitionBuilder;
use Bee\Context\Xml\ParserContext;
use Bee\Context\Xml\XmlNamespace\AbstractSingleBeanDefinitionParser;

/**
 * User: mp
 * Date: 03.07.11
 * Time: 23:35
 */
class Bee_Context_Util_Namespace_ValueFactoryDefinitionParser extends AbstractSingleBeanDefinitionParser {
    protected function getBeanClassName(DOMElement $element) {
        return 'Bee\Context\Util\ValueFactoryBean';
    }

	/**
	 * @param DOMElement $element
	 * @param ParserContext $parserContext
	 * @param BeanDefinitionBuilder $builder
	 */
	protected function doParse(DOMElement $element, ParserContext $parserContext, BeanDefinitionBuilder $builder) {
        $builder->addPropertyValue('sourceValue', $parserContext->getDelegate()->parseValueElement($element, 'string'));
    }
}
