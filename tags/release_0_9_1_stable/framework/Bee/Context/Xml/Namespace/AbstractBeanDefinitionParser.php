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
 * Time: 11:36:56 PM
 */

abstract class Bee_Context_Xml_Namespace_AbstractBeanDefinitionParser implements Bee_Context_Xml_Namespace_IBeanDefinitionParser {

    /** Constant for the id attribute */
    const ID_ATTRIBUTE = 'id';

    public final function parse(DOMElement $element, Bee_Context_Xml_ParserContext $parserContext) {
        $definition = $this->parseInternal($element, $parserContext);
        if (!$parserContext->isNested()) {
            try {
                $id = $this->resolveId($element, $definition, $parserContext);
                if (!Bee_Utils_Strings::hasText($id)) {
                    $parserContext->getReaderContext()->error(
                            "Id is required for element '" + $element->localName + "' when used as a top-level tag", $element);
                }
                $holder = new Bee_Context_Config_BeanDefinitionHolder($definition, $id);
                $this->registerBeanDefinition($holder, $parserContext->getRegistry());
//                if ($this->shouldFireEvents()) {
//                    $componentDefinition = new BeanComponentDefinition(holder);
//                    postProcessComponentDefinition(componentDefinition);
//                    parserContext.registerComponent(componentDefinition);
//                }
            }
            catch (Bee_Context_BeanDefinitionStoreException $ex) {
                $parserContext->getReaderContext()->error($ex->getMessage(), $element);
                return null;
            }
        }
        return $definition;
    }

    /**
     * Resolve the ID for the supplied {@link BeanDefinition}.
     * <p>When using {@link #shouldGenerateId generation}, a name is generated automatically.
     * Otherwise, the ID is extracted from the "id" attribute, potentially with a
     * {@link #shouldGenerateIdAsFallback() fallback} to a generated id.
     * @param DOMElement $element the element that the bean definition has been built from
     * @param Bee_Context_Config_BeanDefinition_Abstract $definition the bean definition to be registered
     * @param Bee_Context_Xml_ParserContext $parserContext the object encapsulating the current state of the parsing process;
     * provides access to a {@link org.springframework.beans.factory.support.BeanDefinitionRegistry}
     * @return string the resolved id
     * @throws BeanDefinitionStoreException if no unique name could be generated
     * for the given bean definition
     */
    protected function resolveId(DOMElement $element, Bee_Context_Config_BeanDefinition_Abstract $definition, Bee_Context_Xml_ParserContext $parserContext) {

        if ($this->shouldGenerateId()) {
            return Bee_Context_Support_BeanDefinitionReaderUtils::generateBeanName($definition, $parserContext->getRegistry(), false);
        }
        else {
            $id = $element->getAttribute(self::ID_ATTRIBUTE);
            if (!Bee_Utils_Strings::hasText($id) && $this->shouldGenerateIdAsFallback()) {
                $id = Bee_Context_Support_BeanDefinitionReaderUtils::generateBeanName($definition, $parserContext->getRegistry(), false);
            }
            return $id;
        }
    }

    /**
     * Register the supplied {@link BeanDefinitionHolder bean} with the supplied
     * {@link BeanDefinitionRegistry registry}.
     * <p>Subclasses can override this method to control whether or not the supplied
     * {@link BeanDefinitionHolder bean} is actually even registered, or to
     * register even more beans.
     * <p>The default implementation registers the supplied {@link BeanDefinitionHolder bean}
     * with the supplied {@link BeanDefinitionRegistry registry} only if the <code>isNested</code>
     * parameter is <code>false</code>, because one typically does not want inner beans
     * to be registered as top level beans.
     * @param Bee_Context_Config_BeanDefinitionHolder $definition the bean definition to be registered
     * @param Bee_Context_Config_IBeanDefinitionRegistry $registry the registry that the bean is to be registered with
     * @see BeanDefinitionReaderUtils#registerBeanDefinition(BeanDefinitionHolder, BeanDefinitionRegistry)
     */
    protected function registerBeanDefinition(Bee_Context_Config_BeanDefinitionHolder $definition, Bee_Context_Config_IBeanDefinitionRegistry $registry) {
        Bee_Context_Support_BeanDefinitionReaderUtils::registerBeanDefinition($definition, $registry);
    }


    /**
     * Central template method to actually parse the supplied {@link Element}
     * into one or more {@link BeanDefinition BeanDefinitions}.
     * @param DOMElement $element	the element that is to be parsed into one or more {@link BeanDefinition BeanDefinitions}
     * @param Bee_Context_Xml_ParserContext $parserContext the object encapsulating the current state of the parsing process;
     * provides access to a {@link org.springframework.beans.factory.support.BeanDefinitionRegistry}
     * @return Bee_Context_Config_BeanDefinition_Abstract the primary {@link BeanDefinition} resulting from the parsing of the supplied {@link Element}
     * @see #parse(org.w3c.dom.Element, ParserContext)
     * @see #postProcessComponentDefinition(org.springframework.beans.factory.parsing.BeanComponentDefinition)
     */
    protected abstract function parseInternal(DOMElement $element, Bee_Context_Xml_ParserContext $parserContext);

    /**
     * Should an ID be generated instead of read from the passed in {@link Element}?
     * <p>Disabled by default; subclasses can override this to enable ID generation.
     * Note that this flag is about <i>always</i> generating an ID; the parser
     * won't even check for an "id" attribute in this case.
     * @return boolean whether the parser should always generate an id
     */
    protected function shouldGenerateId() {
        return false;
    }

    /**
     * Should an ID be generated instead if the passed in {@link Element} does not
     * specify an "id" attribute explicitly?
     * <p>Disabled by default; subclasses can override this to enable ID generation
     * as fallback: The parser will first check for an "id" attribute in this case,
     * only falling back to a generated ID if no value was specified.
     * @return boolean whether the parser should generate an id if no id was specified
     */
    protected function shouldGenerateIdAsFallback() {
        return false;
    }

    /**
     * Controls whether this parser is supposed to fire a
     * {@link org.springframework.beans.factory.parsing.BeanComponentDefinition}
     * event after parsing the bean definition.
     * <p>This implementation returns <code>true</code> by default; that is,
     * an event will be fired when a bean definition has been completely parsed.
     * Override this to return <code>false</code> in order to suppress the event.
     * @return boolean <code>true</code> in order to fire a component registration event
     * after parsing the bean definition; <code>false</code> to suppress the event
     * @see #postProcessComponentDefinition
     * @see org.springframework.beans.factory.parsing.ReaderContext#fireComponentRegistered
     */
//    protected function shouldFireEvents() {
//        return true;
//    }

    /**
     * Hook method called after the primary parsing of a
     * {@link BeanComponentDefinition} but before the
     * {@link BeanComponentDefinition} has been registered with a
     * {@link org.springframework.beans.factory.support.BeanDefinitionRegistry}.
     * <p>Derived classes can override this method to supply any custom logic that
     * is to be executed after all the parsing is finished.
     * <p>The default implementation is a no-op.
     * @param componentDefinition the {@link BeanComponentDefinition} that is to be processed
     */
//    protected function postProcessComponentDefinition(BeanComponentDefinition componentDefinition) {
//    }

}
