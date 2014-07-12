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
use Bee\Context\Config\BeanDefinitionHolder;
use Bee\Context\Config\IBeanDefinition;
use Bee\Context\Config\IBeanDefinitionRegistry;
use Bee_Context_BeanDefinitionStoreException;
use Bee_Context_Config_BeanDefinition_Generic;
use Bee_Utils_Strings;

/**
 * Enter description here...
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 * @author Benjamin Hartmann
 */
class BeanDefinitionReaderUtils {


	const GENERATED_BEAN_NAME_SEPARATOR = '#';

	/**
	 * Create a new GenericBeanDefinition for the given parent name and class name,
	 * eagerly loading the bean class if a ClassLoader has been specified.
	 *
	 * @param string $parentName the name of the parent bean, if any
	 * @param string $className the name of the bean class, if any
	 * @return \Bee_Context_Config_BeanDefinition_Abstract the bean definition
	 */
	public static function createBeanDefinition($parentName, $className) {
		$bd = new Bee_Context_Config_BeanDefinition_Generic();
		$bd->setParentName($parentName);
		if (!is_null($className)) {
			$bd->setBeanClassName($className);
		}
		return $bd;
	}


	/**
	 * Generate a bean name for the given bean definition, unique within the
	 * given bean factory.
	 *
	 * @param IBeanDefinition $definition the bean definition to generate a bean name for
	 * @param IBeanDefinitionRegistry $registry the bean factory that the definition is going to be
	 * registered with (to check for existing bean names)
	 * @param boolean $isInnerBean $isInnerBean whether the given bean definition will be registered
	 * as inner bean or as top-level bean (allowing for special name generation
	 * for inner beans versus top-level beans)
	 * @throws Bee_Context_BeanDefinitionStoreException
	 * @return String the generated bean name
	 */
	public static function generateBeanName(IBeanDefinition $definition, IBeanDefinitionRegistry $registry, $isInnerBean = false) {

		$generatedBeanName = $definition->getBeanClassName();
		if (!Bee_Utils_Strings::hasText($generatedBeanName)) {
			if (Bee_Utils_Strings::hasText($parentName = $definition->getParentName())) {
				$generatedBeanName = $parentName . '$child';
			} else if (Bee_Utils_Strings::hasText($factoryBeanName = $definition->getFactoryBeanName())) {
				$generatedBeanName = $factoryBeanName . '$created';
			}
		}
		if (!Bee_Utils_Strings::hasText($generatedBeanName)) {
			throw new Bee_Context_BeanDefinitionStoreException("Unnamed bean definition specifies neither 'class' nor 'parent' nor 'factory-bean' - can't generate bean name");
		}

		$id = $generatedBeanName;

		// @todo: special name generation for inner beans (see below)
		// Top-level bean: use plain class name.
		// Increase counter until the id is unique.
		$counter = -1;
		while ($counter == -1 || $registry->containsBeanDefinition($id)) {
			$counter++;
			$id = $generatedBeanName . self::GENERATED_BEAN_NAME_SEPARATOR . $counter;
		}

		// @todo: this should be used for name generation of inner beans. right now we don't have a good identity function for PHP
		// objects. (btw, is this necessary at all?)
//		if ($isInnerBean) {
		// Inner bean: generate identity hashcode suffix.
//			$id = $generatedBeanName + self::GENERATED_BEAN_NAME_SEPARATOR + ObjectUtils.getIdentityHexString(definition);
//		} else {
		// shold wrap the above code here...
//		}
		return $id;
	}

	/**
	 * Register the given bean definition with the given bean factory.
	 *
	 * @param BeanDefinitionHolder $definitionHolder
	 * @param IBeanDefinitionRegistry $registry
	 * @return void
	 */
	public static function registerBeanDefinition(
			BeanDefinitionHolder $definitionHolder, IBeanDefinitionRegistry $registry) {

		// Register bean definition under primary name.
		$beanName = $definitionHolder->getBeanName();
		$registry->registerBeanDefinition($beanName, $definitionHolder->getBeanDefinition());

		// Register aliases for bean name, if any.
		$aliases = $definitionHolder->getAliases();
		if (!is_null($aliases)) {
			foreach ($aliases as $alias) {
				$registry->registerAlias($beanName, $alias);
			}
		}
	}
}