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
 * User: mp
 * Date: Feb 17, 2010
 * Time: 11:54:52 PM
 */

abstract class Bee_Context_Xml_Namespace_AbstractSingleBeanDefinitionParser extends Bee_Context_Xml_Namespace_AbstractBeanDefinitionParser {

    /**
     * Creates a {@link BeanDefinitionBuilder} instance for the
     * {@link #getBeanClass bean Class} and passes it to the
     * {@link #doParse} strategy method.
     * @param element the element that is to be parsed into a single BeanDefinition
     * @param parserContext the object encapsulating the current state of the parsing process
     * @return Bee_Context_Config_BeanDefinition_Abstract the BeanDefinition resulting from the parsing of the supplied {@link Element}
     * @throws IllegalStateException if the bean {@link Class} returned from
     * {@link #getBeanClass(org.w3c.dom.Element)} is <code>null</code>
     * @see #doParse
     */
    protected final function parseInternal(DOMElement $element, Bee_Context_Xml_ParserContext $parserContext) {
        $builder = Bee_Context_Support_BeanDefinitionBuilder::genericBeanDefinition();
        $parentName = $this->getParentName($element);
        if ($parentName != null) {
            $builder->setParentName($parentName);
        }
        $beanClassName = $this->getBeanClassName($element);
        if ($beanClassName != null) {
            $builder->getBeanDefinition()->setBeanClassName($beanClassName);
        }

        $this->parseDependsOn($element, $builder->getBeanDefinition());

        Bee_Context_Xml_Utils::parseScopeAttribute($element, $builder->getBeanDefinition(), $parserContext->getContainingBeanDefinition());

        $this->doParse($element, $parserContext, $builder);
        return $builder->getBeanDefinition();
    }

    /**
     * Determine the name for the parent of the currently parsed bean,
     * in case of the current bean being defined as a child bean.
     * <p>The default implementation returns the value of the parent attribute on the element.
     * @param element the <code>Element</code> that is being parsed
     * @return string the name of the parent bean for the currently parsed bean,
     * or <code>null</code> if none
     */
    protected function getParentName(DOMElement $element) {
        return Bee_Context_Xml_Utils::parseParentAttribute($element);
    }

    /**
     * Determine the bean class name corresponding to the supplied {@link Element}.
     * @param element the <code>Element</code> that is being parsed
     * @return string the class name of the bean that is being defined via parsing
     * the supplied <code>Element</code>, or <code>null</code> if none
     * @see #getBeanClass
     */
    protected function getBeanClassName(DOMElement $element) {
        return null;
    }

    /**
     * Parse the supplied {@link Element} and populate the supplied
     * {@link BeanDefinitionBuilder} as required.
     * <p>The default implementation delegates to the <code>doParse</code>
     * version without ParserContext argument.
     * @param element the XML element being parsed
     * @param parserContext the object encapsulating the current state of the parsing process
     * @param builder used to define the <code>BeanDefinition</code>
     * @see #doParse(Element, BeanDefinitionBuilder)
     */
    protected function doParse(DOMElement $element, Bee_Context_Xml_ParserContext $parserContext, Bee_Context_Support_BeanDefinitionBuilder $builder) {
    }

    protected function parseDependsOn(DOMElement $ele, Bee_Context_Config_IBeanDefinition $bd) {
        Bee_Context_Xml_Utils::parseDependsOnAttribute($ele, $bd);
    }
}
?>