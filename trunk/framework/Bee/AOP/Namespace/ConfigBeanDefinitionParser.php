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
 * Date: Feb 18, 2010
 * Time: 6:46:56 AM
 */

class Bee_AOP_Namespace_ConfigBeanDefinitionParser /*implements Bee_Context_Xml_Namespace_IBeanDefinitionParser*/ {

//    const ASPECT = "aspect";
//    const EXPRESSION = "expression";
//    const ID = "id";
//    const POINTCUT = "pointcut";
//    const ADVICE_BEAN_NAME = "adviceBeanName";
//    const ADVISOR = "advisor";
//    const ADVICE_REF = "advice-ref";
//    const POINTCUT_REF = "pointcut-ref";
//    const REF = "ref";
//    const BEFORE = "before";
//    const DECLARE_PARENTS = "declare-parents";
//    const TYPE_PATTERN = "types-matching";
//    const DEFAULT_IMPL = "default-impl";
//    const DELEGATE_REF = "delegate-ref";
//    const IMPLEMENT_INTERFACE = "implement-interface";
//    const AFTER = "after";
//    const AFTER_RETURNING_ELEMENT = "after-returning";
//    const AFTER_THROWING_ELEMENT = "after-throwing";
//    const AROUND = "around";
//    const RETURNING = "returning";
//    const RETURNING_PROPERTY = "returningName";
//    const THROWING = "throwing";
//    const THROWING_PROPERTY = "throwingName";
//    const ARG_NAMES = "arg-names";
//    const ARG_NAMES_PROPERTY = "argumentNames";
//    const ASPECT_NAME_PROPERTY = "aspectName";
//    const DECLARATION_ORDER_PROPERTY = "declarationOrder";
//    const ORDER_PROPERTY = "order";
//    const METHOD_INDEX = 0;
//    const POINTCUT_INDEX = 1;
//    const ASPECT_INSTANCE_FACTORY_INDEX = 2;
//
//    /**
//     * @var Bee_Context_Support_ParseState
//     */
//    private $parseState = null;
//
//    public function __construct() {
//        $this->parseState = new Bee_Context_Support_ParseState();
//    }
//
//    public function parse(DOMElement $element, Bee_Context_Xml_ParserContext $parserContext) {
//        $compositeDef =
//                new Bee_Context_Config_CompositeComponentDefinition($element->tagName);
//
//        $parserContext->pushContainingComponent($compositeDef);
//
//        $this->configureAutoProxyCreator($parserContext, $element);
//
//        foreach($element->childNodes as $node) {
//            if ($node instanceof DOMElement) {
//                $localName = $node->localName;
//                if (self::POINTCUT == $localName) {
//                    $this->parsePointcut($node, $parserContext);
//                }
//                else if (self::ADVISOR == $localName) {
//                    $this->parseAdvisor($node, $parserContext);
//                }
//                else if (self::ASPECT == $localName) {
//                    $this->parseAspect($node, $parserContext);
//                }
//            }
//        }
//
//        $parserContext->popAndRegisterContainingComponent();
//        return null;
//    }
//
//    /**
//     * Configures the auto proxy creator needed to support the {@link BeanDefinition BeanDefinitions}
//     * created by the '<code>&lt;aop:config/&gt;</code>' tag. Will force class proxying if the
//     * '<code>proxy-target-class</code>' attribute is set to '<code>true</code>'.
//     * @see AopNamespaceUtils
//     */
//    private function configureAutoProxyCreator(Bee_Context_Xml_ParserContext $parserContext, DOMElement $element) {
//        AopNamespaceUtils.registerAspectJAutoProxyCreatorIfNecessary(parserContext, element);
//    }
//
//    /**
//     * Parses the supplied <code>&lt;advisor&gt;</code> element and registers the resulting
//     * {@link org.springframework.aop.Advisor} and any resulting {@link org.springframework.aop.Pointcut}
//     * with the supplied {@link BeanDefinitionRegistry}.
//     */
//    private void parseAdvisor(Element advisorElement, ParserContext parserContext) {
//        AbstractBeanDefinition advisorDef = createAdvisorBeanDefinition(advisorElement, parserContext);
//        String id = advisorElement.getAttribute(ID);
//
//        try {
//            this.parseState.push(new AdvisorEntry(id));
//            String advisorBeanName = id;
//            if (StringUtils.hasText(advisorBeanName)) {
//                parserContext.getRegistry().registerBeanDefinition(advisorBeanName, advisorDef);
//            }
//            else {
//                advisorBeanName = parserContext.getReaderContext().registerWithGeneratedName(advisorDef);
//            }
//
//            Object pointcut = parsePointcutProperty(advisorElement, parserContext);
//            if (pointcut instanceof BeanDefinition) {
//                advisorDef.getPropertyValues().addPropertyValue(POINTCUT, pointcut);
//                parserContext.registerComponent(
//                        new AdvisorComponentDefinition(advisorBeanName, advisorDef, (BeanDefinition) pointcut));
//            }
//            else if (pointcut instanceof String) {
//                advisorDef.getPropertyValues().addPropertyValue(POINTCUT, new RuntimeBeanReference((String) pointcut));
//                parserContext.registerComponent(
//                        new AdvisorComponentDefinition(advisorBeanName, advisorDef));
//            }
//        }
//        finally {
//            this.parseState.pop();
//        }
//    }
//
//    /**
//     * Create a {@link RootBeanDefinition} for the advisor described in the supplied. Does <strong>not</strong>
//     * parse any associated '<code>pointcut</code>' or '<code>pointcut-ref</code>' attributes.
//     */
//    private AbstractBeanDefinition createAdvisorBeanDefinition(Element advisorElement, ParserContext parserContext) {
//        RootBeanDefinition advisorDefinition = new RootBeanDefinition(DefaultBeanFactoryPointcutAdvisor.class);
//        advisorDefinition.setSource(parserContext.extractSource(advisorElement));
//
//        String adviceRef = advisorElement.getAttribute(ADVICE_REF);
//        if (!StringUtils.hasText(adviceRef)) {
//            parserContext.getReaderContext().error(
//                    "'advice-ref' attribute contains empty value.", advisorElement, this.parseState.snapshot());
//        }
//        else {
//            advisorDefinition.getPropertyValues().addPropertyValue(
//                    ADVICE_BEAN_NAME, new RuntimeBeanNameReference(adviceRef));
//        }
//
//        if (advisorElement.hasAttribute(ORDER_PROPERTY)) {
//            advisorDefinition.getPropertyValues().addPropertyValue(
//                    ORDER_PROPERTY, advisorElement.getAttribute(ORDER_PROPERTY));
//        }
//
//        return advisorDefinition;
//    }
//
//    private void parseAspect(Element aspectElement, ParserContext parserContext) {
//        String aspectId = aspectElement.getAttribute(ID);
//        String aspectName = aspectElement.getAttribute(REF);
//
//        try {
//            this.parseState.push(new AspectEntry(aspectId, aspectName));
//            List beanDefinitions = new ArrayList();
//            List beanReferences = new ArrayList();
//
//            List declareParents = DomUtils.getChildElementsByTagName(aspectElement, DECLARE_PARENTS);
//            for (int i = METHOD_INDEX; i < declareParents.size(); i++) {
//                Element declareParentsElement = (Element) declareParents.get(i);
//                beanDefinitions.add(parseDeclareParents(declareParentsElement, parserContext));
//            }
//
//            // We have to parse "advice" and all the advice kinds in one loop, to get the
//            // ordering semantics right.
//            NodeList nodeList = aspectElement.getChildNodes();
//            boolean adviceFoundAlready = false;
//            for (int i = 0; i < nodeList.getLength(); i++) {
//                Node node = nodeList.item(i);
//                if (isAdviceNode(node)) {
//                    if (!adviceFoundAlready) {
//                        adviceFoundAlready = true;
//                        if (!StringUtils.hasText(aspectName)) {
//                            parserContext.getReaderContext().error(
//                                    "<aspect> tag needs aspect bean reference via 'ref' attribute when declaring advices.",
//                                    aspectElement, this.parseState.snapshot());
//                            return;
//                        }
//                        beanReferences.add(new RuntimeBeanReference(aspectName));
//                    }
//                    AbstractBeanDefinition advisorDefinition = parseAdvice(
//                            aspectName, i, aspectElement, (Element) node, parserContext, beanDefinitions, beanReferences);
//                    beanDefinitions.add(advisorDefinition);
//                }
//            }
//
//            AspectComponentDefinition aspectComponentDefinition = createAspectComponentDefinition(
//                    aspectElement, aspectId, beanDefinitions, beanReferences, parserContext);
//            parserContext.pushContainingComponent(aspectComponentDefinition);
//
//            List pointcuts = DomUtils.getChildElementsByTagName(aspectElement, POINTCUT);
//            for (int i = 0; i < pointcuts.size(); i++) {
//                Element pointcutElement = (Element) pointcuts.get(i);
//                parsePointcut(pointcutElement, parserContext);
//            }
//
//            parserContext.popAndRegisterContainingComponent();
//        }
//        finally {
//            this.parseState.pop();
//        }
//    }
//
//    private AspectComponentDefinition createAspectComponentDefinition(
//            Element aspectElement, String aspectId, List beanDefs, List beanRefs, ParserContext parserContext) {
//
//        BeanDefinition[] beanDefArray = (BeanDefinition[]) beanDefs.toArray(new BeanDefinition[beanDefs.size()]);
//        BeanReference[] beanRefArray = (BeanReference[]) beanRefs.toArray(new BeanReference[beanRefs.size()]);
//        Object source = parserContext.extractSource(aspectElement);
//        return new AspectComponentDefinition(aspectId, beanDefArray, beanRefArray, source);
//    }
//
//    /**
//     * Return <code>true</code> if the supplied node describes an advice type. May be one of:
//     * '<code>before</code>', '<code>after</code>', '<code>after-returning</code>',
//     * '<code>after-throwing</code>' or '<code>around</code>'.
//     */
//    private boolean isAdviceNode(Node aNode) {
//        if (!(aNode instanceof Element)) {
//            return false;
//        }
//        else {
//            String name = aNode.getLocalName();
//            return (BEFORE.equals(name) || AFTER.equals(name) || AFTER_RETURNING_ELEMENT.equals(name) ||
//                    AFTER_THROWING_ELEMENT.equals(name) || AROUND.equals(name));
//        }
//    }
//
//    /**
//     * Parse a '<code>declare-parents</code>' element and register the appropriate
//     * DeclareParentsAdvisor with the BeanDefinitionRegistry encapsulated in the
//     * supplied ParserContext.
//     */
//    private AbstractBeanDefinition parseDeclareParents(Element declareParentsElement, ParserContext parserContext) {
//        BeanDefinitionBuilder builder = BeanDefinitionBuilder.rootBeanDefinition(DeclareParentsAdvisor.class);
//        builder.addConstructorArgValue(declareParentsElement.getAttribute(IMPLEMENT_INTERFACE));
//        builder.addConstructorArgValue(declareParentsElement.getAttribute(TYPE_PATTERN));
//
//        String defaultImpl = declareParentsElement.getAttribute(DEFAULT_IMPL);
//        String delegateRef = declareParentsElement.getAttribute(DELEGATE_REF);
//
//        if (StringUtils.hasText(defaultImpl) && !StringUtils.hasText(delegateRef)) {
//            builder.addConstructorArgValue(defaultImpl);
//        }
//        else if (StringUtils.hasText(delegateRef) && !StringUtils.hasText(defaultImpl)) {
//            builder.addConstructorArgReference(delegateRef);
//        }
//        else {
//            parserContext.getReaderContext().error(
//                    "Exactly one of the " + DEFAULT_IMPL + " or " + DELEGATE_REF + " attributes must be specified",
//                    declareParentsElement, this.parseState.snapshot());
//        }
//
//        AbstractBeanDefinition definition = builder.getBeanDefinition();
//        definition.setSource(parserContext.extractSource(declareParentsElement));
//        parserContext.getReaderContext().registerWithGeneratedName(definition);
//        return definition;
//    }
//
//    /**
//     * Parses one of '<code>before</code>', '<code>after</code>', '<code>after-returning</code>',
//     * '<code>after-throwing</code>' or '<code>around</code>' and registers the resulting
//     * BeanDefinition with the supplied BeanDefinitionRegistry.
//     * @return the generated advice RootBeanDefinition
//     */
//    private AbstractBeanDefinition parseAdvice(
//            String aspectName, int order, Element aspectElement, Element adviceElement, ParserContext parserContext,
//            List beanDefinitions, List beanReferences) {
//
//        try {
//            this.parseState.push(new AdviceEntry(adviceElement.getLocalName()));
//
//            // create the method factory bean
//            RootBeanDefinition methodDefinition = new RootBeanDefinition(MethodLocatingFactoryBean.class);
//            methodDefinition.getPropertyValues().addPropertyValue("targetBeanName", aspectName);
//            methodDefinition.getPropertyValues().addPropertyValue("methodName", adviceElement.getAttribute("method"));
//            methodDefinition.setSynthetic(true);
//
//            // create instance factory definition
//            RootBeanDefinition aspectFactoryDef =
//                    new RootBeanDefinition(SimpleBeanFactoryAwareAspectInstanceFactory.class);
//            aspectFactoryDef.getPropertyValues().addPropertyValue("aspectBeanName", aspectName);
//            aspectFactoryDef.setSynthetic(true);
//
//            // register the pointcut
//            AbstractBeanDefinition adviceDef = createAdviceDefinition(
//                    adviceElement, parserContext, aspectName, order, methodDefinition, aspectFactoryDef,
//                    beanDefinitions, beanReferences);
//
//            // configure the advisor
//            RootBeanDefinition advisorDefinition = new RootBeanDefinition(AspectJPointcutAdvisor.class);
//            advisorDefinition.setSource(parserContext.extractSource(adviceElement));
//            advisorDefinition.getConstructorArgumentValues().addGenericArgumentValue(adviceDef);
//            if (aspectElement.hasAttribute(ORDER_PROPERTY)) {
//                advisorDefinition.getPropertyValues().addPropertyValue(
//                        ORDER_PROPERTY, aspectElement.getAttribute(ORDER_PROPERTY));
//            }
//
//            // register the final advisor
//            parserContext.getReaderContext().registerWithGeneratedName(advisorDefinition);
//
//            return advisorDefinition;
//        }
//        finally {
//            this.parseState.pop();
//        }
//    }
//
//    /**
//     * Creates the RootBeanDefinition for a POJO advice bean. Also causes pointcut
//     * parsing to occur so that the pointcut may be associate with the advice bean.
//     * This same pointcut is also configured as the pointcut for the enclosing
//     * Advisor definition using the supplied MutablePropertyValues.
//     */
//    private AbstractBeanDefinition createAdviceDefinition(
//            Element adviceElement, ParserContext parserContext, String aspectName, int order,
//            RootBeanDefinition methodDef, RootBeanDefinition aspectFactoryDef, List beanDefinitions, List beanReferences) {
//
//        RootBeanDefinition adviceDefinition = new RootBeanDefinition(getAdviceClass(adviceElement));
//        adviceDefinition.setSource(parserContext.extractSource(adviceElement));
//
//        adviceDefinition.getPropertyValues().addPropertyValue(
//                ASPECT_NAME_PROPERTY, aspectName);
//        adviceDefinition.getPropertyValues().addPropertyValue(
//                DECLARATION_ORDER_PROPERTY, new Integer(order));
//
//        if (adviceElement.hasAttribute(RETURNING)) {
//            adviceDefinition.getPropertyValues().addPropertyValue(
//                    RETURNING_PROPERTY, adviceElement.getAttribute(RETURNING));
//        }
//        if (adviceElement.hasAttribute(THROWING)) {
//            adviceDefinition.getPropertyValues().addPropertyValue(
//                    THROWING_PROPERTY, adviceElement.getAttribute(THROWING));
//        }
//        if (adviceElement.hasAttribute(ARG_NAMES)) {
//            adviceDefinition.getPropertyValues().addPropertyValue(
//                    ARG_NAMES_PROPERTY, adviceElement.getAttribute(ARG_NAMES));
//        }
//
//        ConstructorArgumentValues cav = adviceDefinition.getConstructorArgumentValues();
//        cav.addIndexedArgumentValue(METHOD_INDEX, methodDef);
//
//        Object pointcut = parsePointcutProperty(adviceElement, parserContext);
//        if (pointcut instanceof BeanDefinition) {
//            cav.addIndexedArgumentValue(POINTCUT_INDEX, pointcut);
//            beanDefinitions.add(pointcut);
//        }
//        else if (pointcut instanceof String) {
//            RuntimeBeanReference pointcutRef = new RuntimeBeanReference((String) pointcut);
//            cav.addIndexedArgumentValue(POINTCUT_INDEX, pointcutRef);
//            beanReferences.add(pointcutRef);
//        }
//
//        cav.addIndexedArgumentValue(ASPECT_INSTANCE_FACTORY_INDEX, aspectFactoryDef);
//
//        return adviceDefinition;
//    }
//
//    /**
//     * Gets the advice implementation class corresponding to the supplied {@link Element}.
//     */
//    private Class getAdviceClass(Element adviceElement) {
//        String elementName = adviceElement.getLocalName();
//        if (BEFORE.equals(elementName)) {
//            return AspectJMethodBeforeAdvice.class;
//        }
//        else if (AFTER.equals(elementName)) {
//            return AspectJAfterAdvice.class;
//        }
//        else if (AFTER_RETURNING_ELEMENT.equals(elementName)) {
//            return AspectJAfterReturningAdvice.class;
//        }
//        else if (AFTER_THROWING_ELEMENT.equals(elementName)) {
//            return AspectJAfterThrowingAdvice.class;
//        }
//        else if (AROUND.equals(elementName)) {
//            return AspectJAroundAdvice.class;
//        }
//        else {
//            throw new IllegalArgumentException("Unknown advice kind [" + elementName + "].");
//        }
//    }
//
//    /**
//     * Parses the supplied <code>&lt;pointcut&gt;</code> and registers the resulting
//     * Pointcut with the BeanDefinitionRegistry.
//     */
//    private AbstractBeanDefinition parsePointcut(Element pointcutElement, ParserContext parserContext) {
//        String id = pointcutElement.getAttribute(ID);
//        String expression = pointcutElement.getAttribute(EXPRESSION);
//
//        AbstractBeanDefinition pointcutDefinition = null;
//
//        try {
//            this.parseState.push(new PointcutEntry(id));
//            pointcutDefinition = createPointcutDefinition(expression);
//            pointcutDefinition.setSource(parserContext.extractSource(pointcutElement));
//
//            String pointcutBeanName = id;
//            if (StringUtils.hasText(pointcutBeanName)) {
//                parserContext.getRegistry().registerBeanDefinition(pointcutBeanName, pointcutDefinition);
//            }
//            else {
//                pointcutBeanName = parserContext.getReaderContext().registerWithGeneratedName(pointcutDefinition);
//            }
//
//            parserContext.registerComponent(
//                    new PointcutComponentDefinition(pointcutBeanName, pointcutDefinition, expression));
//        }
//        finally {
//            this.parseState.pop();
//        }
//
//        return pointcutDefinition;
//    }
//
//    /**
//     * Parses the <code>pointcut</code> or <code>pointcut-ref</code> attributes of the supplied
//     * {@link Element} and add a <code>pointcut</code> property as appropriate. Generates a
//     * {@link org.springframework.beans.factory.config.BeanDefinition} for the pointcut if  necessary
//     * and returns its bean name, otherwise returns the bean name of the referred pointcut.
//     */
//    private Object parsePointcutProperty(Element element, ParserContext parserContext) {
//        if (element.hasAttribute(POINTCUT) && element.hasAttribute(POINTCUT_REF)) {
//            parserContext.getReaderContext().error(
//                    "Cannot define both 'pointcut' and 'pointcut-ref' on <advisor> tag.",
//                    element, this.parseState.snapshot());
//            return null;
//        }
//        else if (element.hasAttribute(POINTCUT)) {
//            // Create a pointcut for the anonymous pc and register it.
//            String expression = element.getAttribute(POINTCUT);
//            AbstractBeanDefinition pointcutDefinition = createPointcutDefinition(expression);
//            pointcutDefinition.setSource(parserContext.extractSource(element));
//            return pointcutDefinition;
//        }
//        else if (element.hasAttribute(POINTCUT_REF)) {
//            String pointcutRef = element.getAttribute(POINTCUT_REF);
//            if (!StringUtils.hasText(pointcutRef)) {
//                parserContext.getReaderContext().error(
//                        "'pointcut-ref' attribute contains empty value.", element, this.parseState.snapshot());
//                return null;
//            }
//            return pointcutRef;
//        }
//        else {
//            parserContext.getReaderContext().error(
//                    "Must define one of 'pointcut' or 'pointcut-ref' on <advisor> tag.",
//                    element, this.parseState.snapshot());
//            return null;
//        }
//    }
//
//    /**
//     * Creates a {@link BeanDefinition} for the {@link AspectJExpressionPointcut} class using
//     * the supplied pointcut expression.
//     */
//    protected AbstractBeanDefinition createPointcutDefinition(String expression) {
//        RootBeanDefinition beanDefinition = new RootBeanDefinition(AspectJExpressionPointcut.class);
//        beanDefinition.setScope(BeanDefinition.SCOPE_PROTOTYPE);
//        beanDefinition.setSynthetic(true);
//        beanDefinition.getPropertyValues().addPropertyValue(EXPRESSION, expression);
//        return beanDefinition;
//    }
}

?>