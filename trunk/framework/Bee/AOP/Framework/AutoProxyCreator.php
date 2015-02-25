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
use Bee\Context\BeanCreationException;
use Bee\Context\Config\IContextAware;
use Bee\Context\Config\IInitializingBean;
use Bee\Context\Config\IInstantiationAwareBeanPostProcessor;
use Bee\Context\Support\ContextUtils;
use Bee\IContext;

/**
 * User: mp
 * Date: Feb 18, 2010
 * Time: 9:18:23 AM
 */

class Bee_AOP_Framework_AutoProxyCreator implements IInstantiationAwareBeanPostProcessor, IContextAware, IInitializingBean {
    use \Bee\Context\Config\TContextAware;

    const DO_NOT_PROXY = '<<DO_NOT_PROXY>>';

    /**
     * @var string
     */
    private $cachedAdvisorBeanNames;

    public function afterPropertiesSet() {
    }

    public function postProcessBeforeInstantiation($className, $beanName) {
        echo "COULD POST-PROCESS $beanName of type $className<hr/>";
        return null;
    }

    public function postProcessAfterInstantiation($bean, $beanName) {
        return true;
    }

    function postProcessBeforeInitialization($bean, $beanName) {
        return $bean;
    }

    function postProcessAfterInitialization($bean, $beanName) {
//        $advices = $this->getAdvicesAndAdvisorsForBean(get_class($bean), $beanName);
//        if(is_array($advices) && count($advices) > 0) {
//            var_dump($advices);
//        } else {
//            echo 'no advices for bean '.$beanName.'<hr/>';
//        }
        return $bean;
    }

	/**
	 * @param $beanClassName
	 * @param $beanName
	 * @return string|the
	 */
	protected function getAdvicesAndAdvisorsForBean($beanClassName, $beanName) {
        $advisors = $this->findEligibleAdvisors($beanClassName, $beanName);
        if (count($advisors) == 0) {
            return self::DO_NOT_PROXY;
        }
        return $advisors;
    }

    /**
     * Find all eligible Advisors for auto-proxying this class.
     * @param string $beanClassName the clazz to find advisors for
     * @param string $beanName the name of the currently proxied bean
     * @return the empty List, not <code>null</code> if there are no pointcuts or interceptors
     * @see #findCandidateAdvisors
     * @see #sortAdvisors
     * @see #extendAdvisors
     */
    protected function findEligibleAdvisors($beanClassName, $beanName) {
        $candidateAdvisors = $this->findCandidateAdvisors();
        $eligibleAdvisors = Bee_AOP_Support_Utils::findAdvisorsThatCanApply($candidateAdvisors, $beanClassName);

        if (count($eligibleAdvisors) > 0) {
//            $eligibleAdvisors = $this->sortAdvisors($eligibleAdvisors); // todo: implement sorting...
        }
//        $this->extendAdvisors($eligibleAdvisors);
        return $eligibleAdvisors;
    }

	/**
	 * Find all candidate Advisors to use in auto-proxying.
	 * @throws BeanCreationException
	 * @throws Exception
	 * @return array the List of candidate Advisors
	 */
    protected function findCandidateAdvisors() {
        // Determine list of advisor bean names, if not cached already.
        $advisorNames = null;

        $advisorNames = $this->cachedAdvisorBeanNames;
        if ($advisorNames == null) {
            // Do not initialize FactoryBeans here: We need to leave all regular beans
            // uninitialized to let the auto-proxy creator apply to them!
            $advisorNames = ContextUtils::beanNamesForTypeIncludingAncestors($this->context, 'Bee_AOP_IAdvisor');
            $this->cachedAdvisorBeanNames = $advisorNames;
        }

        if (count($advisorNames) == 0) {
            return array();
        }

        $advisors = array();
        foreach($advisorNames as $name) {
            if ($this->isEligibleAdvisorBean($name) && !$this->context->isBeanCurrentlyInCreation($name)) {
                try {
                    $advisors[] = $this->context->getBean($name, 'Bee_AOP_IAdvisor');
                }
                catch (BeanCreationException $ex) {
//                    $rootCause = $ex->getMostSpecificCause();
//                    if (rootCause instanceof BeanCurrentlyInCreationException) {
//                        BeanCreationException bce = (BeanCreationException) rootCause;
//                        if (this.beanFactory.isCurrentlyInCreation(bce.getBeanName())) {
//                            if (logger.isDebugEnabled()) {
//                                logger.debug("Ignoring currently created advisor '" + name + "': " + ex.getMessage());
//                            }
//                            // Ignore: indicates a reference back to the bean we're trying to advise.
//                            // We want to find advisors other than the currently created bean itself.
//                            continue;
//                        }
//                    }
                    throw $ex;
                }
            }
        }
        return $advisors;
    }

	/**
	 * @param $beanName
	 * @return bool
	 */
	protected function isEligibleAdvisorBean($beanName) {
        return true;
    }
}