<?php
namespace Bee\Beans;

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
use Bee\Context\InvalidPropertyException;
use Bee\Utils\Types;
use ReflectionClass;
use ReflectionMethod;

/**
 * Enter description here...
 *
 * @author Benjamin Hartmann
 */
class BeanWrapper {

    const GETTER_REGEX = '#^(?:get|is)[A-Z]#';

    /**
     * The target object
     *
     * @var object
     */
    private $object;


    public function __construct($object) {
        $this->object = $object;
    }

    public final function setPropertyValue($name, $value) {
        call_user_func($this->findPropertyAccessor($name, 'set'), $value);
    }

    public final function getPropertyValue($name) {
        return call_user_func($this->findPropertyAccessor($name, array('get', 'is')));
    }

    protected function findPropertyAccessor($propertyName, $prefixes) {
        if(($dotpos = strpos($propertyName, '.')) !== false) {
            $pathElem = substr($propertyName, 0, $dotpos);
            $subProp = substr($propertyName, $dotpos + 1);

            $subValue = call_user_func($this->findPropertyAccessor($pathElem, 'get'));
            if(is_null($subValue)) {
                return null;
            }
            $subBw = new BeanWrapper($subValue);
            return $subBw->findPropertyAccessor($subProp, $prefixes);
        }

        $propertyName = ucfirst($propertyName);
        $prefixes = is_array($prefixes) ? $prefixes : array($prefixes);
        $triedMethods = array();
        foreach($prefixes as $prefix) {
            $methodName = $prefix . $propertyName;
            $method = array($this->object, $methodName);
            if (is_callable($method)) {
                return $method;
            }
            $triedMethods[] = $methodName;
        }
        throw new InvalidPropertyException($propertyName, Types::getType($this->object), 'no such method found: ' . implode('|', $triedMethods));
    }

    public final function setPropertyValueWithPropertyValue(PropertyValue $propertyValue) {
        $this->setPropertyValue($propertyValue->getName(), $propertyValue->getValue());
    }

    /**
     * @param array $propertyValues
     */
    public final function setPropertyValues(array $propertyValues) {
        foreach ($propertyValues as $name => $propertyValue) {
            if (!is_string($propertyValue) && Types::isAssignable($propertyValue, 'Bee\Beans\PropertyValue')) {
                $this->setPropertyValueWithPropertyValue($propertyValue);
            } else {
                $this->setPropertyValue($name, $propertyValue);
            }
        }
    }

    /**
     * @param array $propertyValues
     */
    public final function setWritablePropertyValues(array $propertyValues) {
        foreach ($propertyValues as $name => $propertyValue) {
            try {
                if (!is_string($propertyValue) && Types::isAssignable($propertyValue, 'Bee\Beans\PropertyValue')) {
                    $this->setPropertyValueWithPropertyValue($propertyValue);
                } else {
                    $this->setPropertyValue($name, $propertyValue);
                }
            } catch (InvalidPropertyException $e) {
                // ignored
            }
        }
    }

    /**
     * @return array|null
     */
    public final function getPropertyValues() {
        if (is_null($this->object)) {
            return null;
        }
        $result = array();
        $reflClass = new ReflectionClass($this->object);
        $publicMethods = $reflClass->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($publicMethods as $method) {
            $propName = $method->getShortName();
            if (!$method->isStatic() && preg_match(self::GETTER_REGEX, $propName, $matches) && $method->getNumberOfRequiredParameters() == 0) {
                $propName = lcfirst(substr($propName, strlen($matches[0]) - 1));
                $result[$propName] = $method->invoke($this->object);
            }
        }
        return $result;
    }
}