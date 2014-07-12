<?php
namespace Bee\Context\Util;
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
use Bee_Context_Config_IInitializingBean;
use Bee_Context_IFactoryBean;
use Exception;

/**
 * User: mp
 * Date: 01.07.11
 * Time: 03:16
 */
 
abstract class AbstractFactoryBean implements Bee_Context_IFactoryBean, Bee_Context_Config_IInitializingBean {

    /**
     * @var boolean
     */
    private $singleton;

    private $initialized;

    private $singletonInstance;

    /**
     * @param $singleton boolean
     * @return void
     */
    public function setSingleton($singleton) {
        $this->singleton = $singleton;
    }

    /**
     * @return boolean
     */
    public function isSingleton() {
        return $this->singleton;
    }

    public function afterPropertiesSet() {
        if ($this->isSingleton()) {
            $this->initialized = true;
            $this->singletonInstance = $this->createInstance();
        }
    }

    public function getObject() {
        if ($this->isSingleton()) {
            return ($this->initialized ? $this->singletonInstance : $this->getEarlySingletonInstance());
        } else {
            return $this->createInstance();
        }
    }

    /**
     * Template method that subclasses must override to construct
     * the object returned by this factory.
     * <p>Invoked on initialization of this FactoryBean in case of
     * a singleton; else, on each {@link #getObject()} call.
     * @return mixed the object returned by this factory
     * @throws Exception if an exception occured during object creation
     * @see #getObject()
     */
    protected abstract function &createInstance();

	/**
	 * @throws \Exception
	 */
	private function getEarlySingletonInstance() {
        throw new Exception('Circular references not yet supported');
    }
}
