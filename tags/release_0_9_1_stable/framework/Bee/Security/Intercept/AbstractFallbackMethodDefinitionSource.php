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
 * Time: 7:43:35 PM
 * To change this template use File | Settings | File Templates.
 */

abstract class Bee_Security_Intercept_AbstractFallbackMethodDefinitionSource implements Bee_Security_Intercept_IMethodDefinitionSource {

    const NULL_CONFIG_ATTRIBUTE = '<<NULL_CONFIG_ATTRIBUTE>>';
    /**
     * @var Bee_Security_ConfigAttributeDefinition[] 
     */
    private $attributeCache = array();

    public function getAttributes($object) {

        if ($object instanceof Bee_AOP_Intercept_IMethodInvocation) {
            $target = $object->getThis();
            return $this->getAttributesForMethod($object->getMethod(), $target == null ? null : get_class($target));
        }

        throw new InvalidArgumentException("Object must be a Bee_AOP_Intercept_IMethodInvocation");
    }

    public final function supports($classOrClassName) {
        return Bee_Utils_Types::isAssignable($classOrClassName, 'Bee_AOP_Intercept_IMethodInvocation');
    }

    public function getAttributesForMethod(ReflectionMethod $method, $targetClassOrClassName) {
        // First, see if we have a cached value.
        $targetClassName = $targetClassOrClassName;
        if($targetClassOrClassName instanceof ReflectionClass) {
            $targetClassName = $targetClassOrClassName->getName();
        }

        $cacheKey = $targetClassName.'::'.$method->getName();

        $cached = $this->attributeCache[$cacheKey];
        if ($cached != null) {
            // Value will either be canonical value indicating there is no config attribute,
            // or an actual config attribute.
            return  $cached == self::NULL_CONFIG_ATTRIBUTE ? null : $cached;
        } else {
            // We need to work it out.
            $cfgAtt = $this->computeAttributes($method, $targetClassOrClassName);
            // Put it in the cache.
            if ($cfgAtt == null) {
                $this->attributeCache[$cacheKey] = self::NULL_CONFIG_ATTRIBUTE;
            } else {
                if (Bee_Utils_Logger::isDebugEnabled()) {
                    Bee_Utils_Logger::debug("Adding security method [$cacheKey] with attribute [$cfgAtt]");
                }
                $this->attributeCache[$cacheKey] = $cfgAtt;
            }
            return $cfgAtt;
        }
    }

    /**
     *
     * @param ReflectionMethod $method the method for the current invocation (never <code>null</code>)
     * @param ReflectionClass|string $targetClassOrClassName the target class for this invocation (may be <code>null</code>)
     * @return
     */
    private function computeAttributes(ReflectionMethod $method, $targetClassOrClassName) {
        $targetClass = $targetClassOrClassName;
        if(!$targetClass instanceof ReflectionClass) {
            $targetClass = new ReflectionClass($targetClassOrClassName);
        }

        // The method may be on an interface, but we need attributes from the target class.
        // If the target class is null, the method will be unchanged.
        $specificMethod = Bee_Utils_Reflection::getMostSpecificMethod($method, $targetClass);

        // First try is the method in the target class.
        $attr = $this->findAttributes($specificMethod, $targetClassOrClassName);
        if ($attr != null) {
            return $attr;
        }

        // Second try is the config attribute on the target class.
        $attr = $this->findAttributes($specificMethod->getDeclaringClass());
        if ($attr != null) {
            return $attr;
        }

        if ($specificMethod != $method || $targetClassOrClassName == null) {
            // Fallback is to look at the original method.
            $attr = $this->findAttributes($method, $method->getDeclaringClass());
            if ($attr != null) {
                return $attr;
            }
            // Last fallback is the class of the original method.
            return $this->findAttributes($method->getDeclaringClass());
        }
        return null;

    }

    /**
     * Obtains the security metadata applicable to the specified method invocation.
     *
     * <p>
     * Note that the {@link Method#getDeclaringClass()} may not equal the <code>targetClass</code>.
     * Both parameters are provided to assist subclasses which may wish to provide advanced
     * capabilities related to method metadata being "registered" against a method even if the
     * target class does not declare the method (i.e. the subclass may only inherit the method).
     *
     * @param ReflectionMethod $method the method for the current invocation (never <code>null</code>)
     * @param ReflectionClass|string $targetClassOrClassName the target class for the invocation (may be <code>null</code>)
     * @return the security metadata (or null if no metadata applies)
     */
    protected abstract function findAttributesForMethod(ReflectionMethod $method, $targetClassOrClassName);

    /**
     * Obtains the security metadata registered against the specified class.
     *
     * <p>
     * Subclasses should only return metadata expressed at a class level. Subclasses should NOT
     * aggregate metadata for each method registered against a class, as the abstract superclass
     * will separate invoke {@link #findAttributes(Method, Class)} for individual methods as
     * appropriate.
     *
     * @param ReflectionClass|string $targetClassOrClassName the target class for the invocation (never <code>null</code>)
     * @return the security metadata (or null if no metadata applies)
     */
    protected abstract function findAttributes($targetClassOrClassName);
}
