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
use Bee\Context\Config\IBeanPostProcessor;
use Bee\Context\Config\IContextAware;
use Bee\Framework;
use Bee\IContext;

/**
 * User: mp
 * Date: Feb 19, 2010
 * Time: 10:29:38 PM
 */

class Bee_Security_Config_MethodSecurityInterceptorPostProcessor implements IBeanPostProcessor, IContextAware {

	/**
	 * @var Logger
	 */
	protected static $log;

	/**
	 * @return Logger
	 */
	protected static function getLog() {
		if (!self::$log) {
			self::$log = Framework::getLoggerForClass(__CLASS__);
		}
		return self::$log;
	}

    /**
     * @var IContext
     */
    private $beeContext;

    public function postProcessBeforeInitialization($bean, $beanName) {

        if(Bee_Security_Config_IBeanIds::METHOD_SECURITY_INTERCEPTOR == $beanName &&
                $this->beeContext->containsBean(Bee_Security_Config_IBeanIds::AFTER_INVOCATION_MANAGER)) {
			self::getLog()->debug("Setting AfterInvocationManaer on MethodSecurityInterceptor");
            $bean->setAfterInvocationManager($this->beeContext->getBean(Bee_Security_Config_IBeanIds::AFTER_INVOCATION_MANAGER));
        }

        return $bean;
    }

    public function postProcessAfterInitialization($bean, $beanName) {
        return $bean;
    }

    public function setBeeContext(IContext $context) {
        $this->beeContext = $context;
    }

}