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
use Bee\Context\Config\IInitializingBean;
use Bee\Utils\Assert;
use Bee\Utils\Collections;

/**
 * Created by IntelliJ IDEA.
 * User: mp
 * Date: Feb 19, 2010
 * Time: 9:15:51 PM
 * To change this template use File | Settings | File Templates.
 */

class Bee_Security_Intercept_DelegatingMethodDefinitionSource implements Bee_Security_Intercept_IMethodDefinitionSource, IInitializingBean {

    /**
     * @var Bee_Security_Intercept_IMethodDefinitionSource[]
     */
    private $methodDefinitionSources;

    public function afterPropertiesSet() {
		Assert::isTrue(!Collections::isEmpty($this->methodDefinitionSources), "A list of MethodDefinitionSources is required");
    }

    public function getAttributesForMethod(ReflectionMethod $method, $targetClassOrClassName) {
        foreach($this->methodDefinitionSources as $methodDefinitionSource) {
            $result = $methodDefinitionSource->getAttributesForMethod($method, $targetClassOrClassName);

            if($result != null) {
                return $result;
            }
        }
        return null;
    }

    public function getAttributes($object) {
        foreach($this->methodDefinitionSources as $methodDefinitionSource) {
            $result = $methodDefinitionSource->getAttributes($object);

            if($result != null) {
                return $result;
            }
        }
        return null;
    }

    public function getConfigAttributeDefinitions() {
        $set = array();
        foreach($this->methodDefinitionSources as $methodDefinitionSource) {
            $attrs = $methodDefinitionSource->getConfigAttributeDefinitions();
            if($attrs != null) {
                $set = array_merge($set, $attrs);
            }
        }
        return $set;
    }

    public function supports($classOrClassName) {
        foreach($this->methodDefinitionSources as $methodDefinitionSource) {
            if($methodDefinitionSource->supports($classOrClassName)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Bee_Security_Intercept_IMethodDefinitionSource[] $methodDefinitionSources
     * @return void
     */
    public function setMethodDefinitionSources(array $methodDefinitionSources) {
		Assert::isTrue(!Collections::isEmpty($this->methodDefinitionSources), "A list of MethodDefinitionSources is required");
        $this->methodDefinitionSources = $methodDefinitionSources;
    }
}