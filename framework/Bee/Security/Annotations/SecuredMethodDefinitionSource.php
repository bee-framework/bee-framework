<?php
namespace Bee\Security\Annotations;
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
use Addendum\Annotation;
use Addendum\ReflectionAnnotatedClass;
use Bee\Annotations\Utils;
use Bee\Security\ConfigAttributeDefinition;
use Bee_Security_Intercept_AbstractFallbackMethodDefinitionSource;
use ReflectionClass;
use ReflectionMethod;

/**
 * Created by IntelliJ IDEA.
 * User: mp
 * Date: Feb 19, 2010
 * Time: 6:07:49 PM
 * To change this template use File | Settings | File Templates.
 */

class SecuredMethodDefinitionSource extends Bee_Security_Intercept_AbstractFallbackMethodDefinitionSource {

    const SECURED_ANNOTATION_CLASS_NAME = 'Bee\Security\Annotations\Secured';

    /**
     * @access protected
     * @param ReflectionClass|string $classOrClassName
     * @return ConfigAttributeDefinition
     */
    protected function findAttributes($classOrClassName) {
        if($classOrClassName instanceof ReflectionAnnotatedClass) {
            $cls = $classOrClassName;
        } else if($classOrClassName instanceof ReflectionClass) {
            $cls = new ReflectionAnnotatedClass($classOrClassName->getName());
        } else {
            $cls = new ReflectionAnnotatedClass($classOrClassName);
        }
        return $this->processAnnotation($cls->getAnnotation(self::SECURED_ANNOTATION_CLASS_NAME));
    }

    /**
     * @access protected
     * @param ReflectionMethod $method
     * @param ReflectionClass|string $targetClassOrClassName
     * @return ConfigAttributeDefinition
     */
    protected function findAttributesForMethod(ReflectionMethod $method, $targetClassOrClassName) {
        return $this->processAnnotation(Utils::findAnnotation($method, self::SECURED_ANNOTATION_CLASS_NAME));
    }

    /**
     * @return mixed
     */
    public function getConfigAttributeDefinitions() {
        return null;
    }

    /**
     * @access private
     * @param Annotation $a
     * @return ConfigAttributeDefinition
     */
    private function processAnnotation(Annotation $a) {
        if ($a == null || !($a instanceof Secured)) {
            return null;
        }
        return new ConfigAttributeDefinition($a->value);
    }
}
