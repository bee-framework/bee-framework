<?php
namespace Bee\Context;
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
use Bee\Context\Config\ArrayValue;
use Bee\Context\Config\BeanDefinitionHolder;
use Bee\Context\Config\IBeanDefinition;
use Bee\Context\Config\RuntimeBeanNameReference;
use Bee\Context\Config\RuntimeBeanReference;
use Bee\Context\Config\TypedStringValue;
use Bee\IContext;

/**
 * Enter description here...
 *
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class BeanDefinitionValueResolver {
	
	/**
	 * Enter description here...
	 *
	 * @var AbstractContext
	 */
	private $context;
	
	/**
	 * Enter description here...
	 *
	 * @var string
	 */
	private $beanName;

	/**
	 * Enter description here...
	 *
	 * @param AbstractContext $context
	 * @param String $beanName
	 */
	public function __construct(AbstractContext $context, $beanName) {
		$this->context = $context;
		$this->beanName = $beanName;
	}

	/**
	 * Given a PropertyValue, return a value, resolving any references to other
	 * beans in the factory if necessary. The value could be:
	 * <li>A BeanDefinition, which leads to the creation of a corresponding
	 * new bean instance. Singleton flags and names of such "inner beans"
	 * are always ignored: Inner beans are anonymous prototypes.
	 * <li>A RuntimeBeanReference, which must be resolved.
	 * <li>A ManagedList. This is a special collection that may contain
	 * RuntimeBeanReferences or Collections that will need to be resolved.
	 * <li>A ManagedSet. May also contain RuntimeBeanReferences or
	 * Collections that will need to be resolved.
	 * <li>A ManagedMap. In this case the value may be a RuntimeBeanReference
	 * or Collection that will need to be resolved.
	 * <li>An ordinary object or <code>null</code>, in which case it's left alone.
	 * @param string $argName
	 * @param mixed $value
	 * @return mixed the resolved object
	 */
	public function resolveValueIfNecessary($argName, $value) {

		// We must check each value to see whether it requires a runtime reference
		// to another bean to be resolved.
		if ($value instanceof RuntimeBeanReference) {

			return $this->resolveReference($argName, $value);

		} else if ($value instanceof RuntimeBeanNameReference) {

			return $this->checkBeanNameReference($argName, $value);

		} else if ($value instanceof BeanDefinitionHolder) {
			
			// Resolve BeanDefinitionHolder: contains BeanDefinition with name and aliases.
			return $this->resolveInnerBean($argName, $value->getBeanName(), $value->getBeanDefinition());

		} else if ($value instanceof IBeanDefinition) {

			// Resolve plain BeanDefinition, without contained name: use dummy name.
			return $this->resolveInnerBean($argName, "(inner bean)", $value);

		} else if ($value instanceof ArrayValue) {

			// May need to resolve contained runtime references.
			return $this->resolveArrayValue($argName, $value);

		} else if (is_array($value)) {
            // TODO: deprecated
			// May need to resolve contained runtime references.
			return $this->resolveArray($argName, $value);

		} else if ($value instanceof TypedStringValue) {

			return $value->getValue($this->context->getPropertyEditorRegistry());

		} else {
			// No need to resolve value...
			return $value;
		}
	}

	/**
	 * @param $argName
	 * @param RuntimeBeanNameReference $value
	 * @return null
	 * @throws BeanDefinitionStoreException
	 */
	private function checkBeanNameReference($argName, RuntimeBeanNameReference $value) {
        $ref = $this->findApplicableBeanName($value->getBeanNames(), $this->context);
        if (!$this->context->containsBean($ref)) {
            throw new BeanDefinitionStoreException("Invalid bean name '$ref' in bean reference $argName");
        }
        return $ref;
    }

	/**
	 * Resolve a reference to another bean in the factory.
	 * @param $argName
	 * @param RuntimeBeanReference $ref
	 * @throws BeanCreationException
	 * @return mixed
	 */
	private function resolveReference($argName, RuntimeBeanReference $ref) {
		try {
			if ($ref->isToParent()) {
				if (is_null($this->context->getParent())) {
					// @todo: a lot of debug information is lost here
					throw new BeanCreationException($this->beanName);
				}
//				return $this->context->getParent()->getBean($ref->getBeanName());
				return $this->getBeanFromContext($ref->getBeanNames(), $this->context->getParent());
			} else {
//				$bean = $this->context->getBean($ref->getBeanName());
//				$this->context->registerDependentBean($ref->getBeanName(), $this->beanName);
//                return $bean;
                return $this->getBeanFromContext($ref->getBeanNames(), $this->context);
			}

		} catch (BeansException $ex) {
			// @todo: a lot of debug information is lost here
			throw new BeanCreationException($this->beanName, "Error resolving reference for argument $argName", $ex);
		}
	}

	/**
	 * @param array $beanNames
	 * @param IContext $context
	 * @return null
	 */
	private function findApplicableBeanName(array $beanNames, IContext $context) {
        $result = null;
        foreach($beanNames as $beanName) {
            $result = $beanName;
            if($context->containsBean($beanName)) {
                break;
            }
        }
        return $result;
    }

	/**
	 * @param array $beanNames
	 * @param IContext $context
	 * @return Object
	 */
	private function getBeanFromContext(array $beanNames, IContext $context) {
        $beanName = $this->findApplicableBeanName($beanNames, $context);
        $bean = $context->getBean($beanName);
        if($context == $this->context) {
            $this->context->registerDependentBean($beanName, $this->beanName);
        }
        return $bean;
    }

	/**
	 * Resolve an inner bean definition.
	 * @param String $argName the name of the argument that the inner bean is defined for
	 * @param String $innerBeanName the name of the inner bean
	 * @param IBeanDefinition $innerBeanDefinition the bean definition for the inner bean
	 * @throws BeanCreationException
	 * @return mixed the resolved inner bean instance
	 */
	private function resolveInnerBean($argName, $innerBeanName, IBeanDefinition $innerBeanDefinition) {
		try {
			// Guarantee initialization of beans that the inner bean depends on.
			$dependsOn = $innerBeanDefinition->getDependsOn();
			
			if(!is_null($dependsOn)) {
				foreach ($dependsOn as $dep) {
					$this->context->getBean($dep);
					$this->context->registerDependentBean($dep, $innerBeanName);
				}				
			}
			$innerBean = $this->context->_createBean($innerBeanName, $innerBeanDefinition);
			$this->context->registerDependentBean($innerBeanName, $this->beanName);

			return $innerBean;

		} catch (BeansException $ex) {
			throw new BeanCreationException($this->beanName, 'Error resolving inner bean for argument ' . $argName, $ex);
		}
	}

	/**
	 * @param $argName
	 * @param ArrayValue $arr
	 * @return array
	 */
	private function resolveArrayValue($argName, ArrayValue $arr) {
		return array_map(array($this, '_resolveValueIfNecessary'), $arr->getValue());
	}

	/**
	 * @param $argName
	 * @param array $arr
	 * @return array
	 */
	private function resolveArray($argName, array $arr) {
		return array_map(array($this, '_resolveValueIfNecessary'), $arr);
	}

	/**
	 * @param $value
	 * @return mixed
	 */
	protected function _resolveValueIfNecessary($value) {
		return $this->resolveValueIfNecessary('(array element)', $value);
	}
}