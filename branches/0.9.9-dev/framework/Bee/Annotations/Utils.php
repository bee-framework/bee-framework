<?php
namespace Bee\Annotations;
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
use Addendum\ReflectionAnnotatedClass;
use Addendum\ReflectionAnnotatedMethod;
use ReflectionMethod;

/**
 * User: mp
 * Date: Feb 19, 2010
 * Time: 7:14:44 PM
 */
class Utils {

	/**
	 * Get a single {@link Annotation} of <code>annotationType</code> from the
	 * supplied {@link Method}, traversing its super methods if no annotation
	 * can be found on the given method itself.
	 * <p>Annotations on methods are not inherited by default, so we need to handle
	 * this explicitly. Tge
	 * @param ReflectionMethod $method
	 * @param $annotationClassName
	 * @return mixed the annotation found, or <code>null</code> if none found
	 */
    public static function findAnnotation(ReflectionMethod $method, $annotationClassName) {
        $annotatedMethod = self::getReflectionAnnotatedMethodIfNecessary($method);
        $annotation = $annotatedMethod->getAnnotation($annotationClassName);
        $class = $method->getDeclaringClass();
        while ($annotation == null) {
            $class = $class->getParentClass();
            if (!$class instanceof ReflectionAnnotatedClass) {
                break;
            }
            $equivalentMethod = $class->getMethod($method->getName());
            $annotation = $annotatedMethod->getAnnotation($annotationClassName);
        }
        return $annotation;
    }

    private static function getReflectionAnnotatedMethodIfNecessary(ReflectionMethod $method) {
        if($method instanceof ReflectionAnnotatedMethod) {
            return $method;
        }
        return new ReflectionAnnotatedMethod($method->getDeclaringClass(), $method->getName());
    }
}
