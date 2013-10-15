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
 * Date: Feb 19, 2010
 * Time: 10:46:28 PM
 * To change this template use File | Settings | File Templates.
 */

class Bee_Security_Intercept_MethodDefinitionSourceAdvisor extends Bee_AOP_Support_AbstractPointcutAdvisor implements Bee_Context_Config_IContextAware {

    /**
     * @var Bee_IContext
     */
    private $beeContext;

    /**
     * @var Bee_Security_Intercept_IMethodDefinitionSource
     */
    private $attributeSource;

    /**
     * @var Bee_Security_Intercept_MethodSecurityInterceptor
     */
    private $interceptor;

    /**
     * @var Bee_AOP_IPointcut
     */
    private $pointcut;

    /**
     * @var string 
     */
    private $adviceBeanName;

    //~ Constructors ===================================================================================================

    /**
     * Alternative constructor for situations where we want the advisor decoupled from the advice. Instead the advice
     * bean name should be set. This prevents eager instantiation of the interceptor
     * (and hence the AuthenticationManager). See SEC-773, for example.
     * <p>
     * This is essentially the approach taken by subclasses of {@link AbstractBeanFactoryPointcutAdvisor}, which this
     * class should extend in future. The original hierarchy and constructor have been retained for backwards
     * compatibility.
     *
     * @param adviceBeanName name of the MethodSecurityInterceptor bean
     * @param attributeSource the attribute source (should be the same as the one used on the interceptor)
     */
    public function __construct($adviceBeanName, Bee_Security_Intercept_IMethodDefinitionSource $attributeSource) {
        Bee_Utils_Assert::notNull($adviceBeanName, "The adviceBeanName cannot be null");
        Bee_Utils_Assert::notNull($attributeSource, "The attributeSource cannot be null");

        $this->adviceBeanName = $adviceBeanName;
        $this->attributeSource = $attributeSource;

        $this->pointcut = new Bee_Security_Intercept_MethodDefinitionSourceAdvisor_MethodDefinitionSourcePointcut($this);
    }

    //~ Methods ========================================================================================================

    public function getPointcut() {
        return $this->pointcut;
    }

    public function getAdvice() {
        if ($this->interceptor == null) {
            Bee_Utils_Assert::notNull($this->adviceBeanName, "'adviceBeanName' must be set for use with bean factory lookup.");
            Bee_Utils_Assert::notNull($this->beeContext != null, "BeanFactory must be set to resolve 'adviceBeanName'");
            $this->interceptor = $this->beeContext->getBean($this->adviceBeanName, 'Bee_Security_Intercept_MethodSecurityInterceptor');
        }
        return $this->interceptor;
    }

    public function setBeeContext(Bee_IContext $context) {
        $this->beeContext = $context;
    }

    public function getAttributeSource() {
        return $this->attributeSource;
    }

}
class Bee_Security_Intercept_MethodDefinitionSourceAdvisor_MethodDefinitionSourcePointcut extends Bee_AOP_Support_StaticMethodMatcherPointcut {

    /**
     * @var Bee_Security_Intercept_MethodDefinitionSourceAdvisor
     */
    private $owner;

    public function __construct(Bee_Security_Intercept_MethodDefinitionSourceAdvisor $owner) {
        $this->owner = $owner;
    }

    public function matches(ReflectionMethod $m, ReflectionClass $targetClass) {
        return $this->owner->getAttributeSource()->getAttributesForMethod($m, $targetClass) != null;
    }
}
?>