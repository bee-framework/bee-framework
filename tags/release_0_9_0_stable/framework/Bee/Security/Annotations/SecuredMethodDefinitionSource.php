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
 * Time: 6:07:49 PM
 * To change this template use File | Settings | File Templates.
 */

class Bee_Security_Annotations_SecuredMethodDefinitionSource extends Bee_Security_Intercept_AbstractFallbackMethodDefinitionSource {

    const SECURED_ANNOTATION_CLASS_NAME = 'Bee_Security_Annotations_Secured';

    /**
     * @access protected
     * @param ReflectionClass|string $classOrClassName
     * @return Bee_Security_ConfigAttributeDefinition
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
     * @return Bee_Security_ConfigAttributeDefinition
     */
    protected function findAttributesForMethod(ReflectionMethod $method, $targetClassOrClassName) {
        return $this->processAnnotation(Bee_Annotations_Utils::findAnnotation($method, self::SECURED_ANNOTATION_CLASS_NAME));
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
     * @return Bee_Security_ConfigAttributeDefinition
     */
    private function processAnnotation(Annotation $a) {
        if ($a == null || !($a instanceof Bee_Security_Annotations_Secured)) {
            return null;
        }
        return new Bee_Security_ConfigAttributeDefinition($a->value);
    }
}
