<?php
namespace Bee\Context\Support;
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

use Bee\IContext;
use Bee\Utils\Assert;
use Bee\Utils\Strings;

/**
 * Class ContextUtils
 * @package Bee\Context\Support
 */
abstract class ContextUtils {
	
    /**
     * Return whether the given name is a factory dereference
     * (beginning with the factory dereference prefix).
     * @param string $name the name of the bean
     * @return boolean whether the given name is a factory dereference
     * @see BeanFactory#FACTORY_BEAN_PREFIX
     */
    public static function isFactoryDereference($name) {
        return ($name != null && Strings::startsWith($name, IContext::FACTORY_BEAN_PREFIX));
    }

	/**
	 * Return the actual bean name, stripping out the factory dereference
	 * prefix (if any, also stripping repeated factory prefixes if found).
	 *
	 * @param String $name the name of the bean
	 * @return String the transformed name
	 * @see IContext#FACTORY_BEAN_PREFIX
	 */
	public static function transformedBeanName($name) {
		Assert::notNull($name, '\'name\' must not be null');
		$beanName = $name;
		while(Strings::startsWith($beanName, IContext::FACTORY_BEAN_PREFIX)) {
			$beanName = substr($beanName, strlen(IContext::FACTORY_BEAN_PREFIX));
		}
		return $beanName;
	}

    public static function beanNamesForTypeIncludingAncestors(IContext $beeContext, $className) {
        $result = $beeContext->getBeanNamesForType($className);
        $parent = $beeContext->getParent();
        if ($parent instanceof IContext) {
            $result = array_merge($result, self::beanNamesForTypeIncludingAncestors($parent, $className));
        }
        return $result;
    }
}