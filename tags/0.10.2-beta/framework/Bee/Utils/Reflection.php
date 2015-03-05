<?php
namespace Bee\Utils;
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
use ReflectionClass;
use ReflectionMethod;

/**
 * Enter description here...
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class Reflection {

	/**
	 * @param ReflectionMethod $method
	 * @return bool
	 */
	public static function isCallableRegularMethod(ReflectionMethod $method) {
		return $method->isPublic() && !$method->isAbstract() && !$method->isConstructor();
	}

    /**
     * @static
     * @param ReflectionMethod $method
     * @param ReflectionClass $targetClass
     * @return ReflectionMethod
     */
    public static function getMostSpecificMethod(ReflectionMethod $method, ReflectionClass $targetClass) {
        if ($method != null && $targetClass != null && $targetClass->getName() != $method->getDeclaringClass()) {
            $mostSpecificMethod = $targetClass->getMethod($method->getName());
            if($mostSpecificMethod instanceof ReflectionMethod) {
                $method = $mostSpecificMethod;
            }
        }
        return $method;
    }

	/**
	 * @param $typeName
	 * @param bool $requiredTypeName
	 * @return mixed
	 */
	public static function createInstance($typeName, $requiredTypeName = false) {

        // todo: implement this in a more fancy way...
		Assert::hasText($typeName);

        $inst = new $typeName();
        if($requiredTypeName) {
			Assert::isInstanceOf($requiredTypeName, $inst);
        }

        return $inst;
    }
}
