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
use Bee\Context\Config\IContextAware;
use Bee\Utils\Assert;

/**
 * Created by IntelliJ IDEA.
 * User: mp
 * Date: Feb 19, 2010
 * Time: 10:46:28 PM
 * To change this template use File | Settings | File Templates.
 */

class Bee_Security_Intercept_MethodDefinitionSourceAdvisor extends Bee_AOP_Support_AbstractPointcutAdvisor implements IContextAware {
    use \Bee\Context\Config\TContextAware;

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
     * @param string $adviceBeanName name of the MethodSecurityInterceptor bean
     * @param Bee_Security_Intercept_IMethodDefinitionSource $attributeSource the attribute source (should be the same as the one used on the interceptor)
     */
    public function __construct($adviceBeanName, Bee_Security_Intercept_IMethodDefinitionSource $attributeSource) {
        Assert::notNull($adviceBeanName, "The adviceBeanName cannot be null");
        Assert::notNull($attributeSource, "The attributeSource cannot be null");

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
            Assert::notNull($this->adviceBeanName, "'adviceBeanName' must be set for use with bean factory lookup.");
            Assert::notNull($this->context != null, "BeanFactory must be set to resolve 'adviceBeanName'");
            $this->interceptor = $this->context->getBean($this->adviceBeanName, 'Bee_Security_Intercept_MethodSecurityInterceptor');
        }
        return $this->interceptor;
    }

	/**
	 * @return Bee_Security_Intercept_IMethodDefinitionSource
	 */
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