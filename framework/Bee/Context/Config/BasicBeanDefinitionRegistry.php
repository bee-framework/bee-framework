<?php
namespace Bee\Context\Config;
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

use Bee\Context\Support\ContextUtils;
use Bee_Context_BeanDefinitionStoreException;
use Bee_Context_Config_BeanDefinition_Generic;
use Bee_Context_NoSuchBeanDefinitionException;
use Bee_Utils_Types;
use Bee_Context_AliasRegistry;

class BasicBeanDefinitionRegistry extends Bee_Context_AliasRegistry implements IBeanDefinitionRegistry {
	
	/**
	 * Enter description here...
	 *
	 * @var IBeanDefinition[]
	 */
	private $beanDefinitions = array();
	
    /**
     * BeanPostProcessors to apply in createBean
     * @var BasicBeanDefinitionRegistry[]
     */
	private $beanPostProcessorMap = array();

    /**
     *
     * @var boolean
     */
    private $hasInstantiationAwareBeanPostProcessors;

    /**
     * @var boolean
     */
    private $hasDestructionAwareBeanPostProcessors;

	/**
	 * @var IBeanDefinitionRegistry
	 */
	private $parentRegistry;

	public function containsBeanDefinition($beanName) {
		return array_key_exists($beanName, $this->beanDefinitions);
	}

    /**
     * @throws Bee_Context_NoSuchBeanDefinitionException
     * @param  $beanName
     * @return IBeanDefinition
     */
	public function getBeanDefinition($beanName) {

		if (array_key_exists($beanName, $this->beanDefinitions)) {
			$bd = $this->beanDefinitions[$beanName];
            $parentName = $bd->getParentName();
            if(!is_null($parentName)) {
                $parentName = $this->transformedBeanName($parentName);
                $parentBd = $this->getBeanDefinition($parentName);
                $newBd = new Bee_Context_Config_BeanDefinition_Generic($parentBd);
                $newBd->overrideFrom($bd);
                $newBd->setParentName(null);
                $bd = $newBd;
                $this->beanDefinitions[$beanName] = $bd;
            }
            return $bd;
		}
		if(!is_null($this->parentRegistry)) {
			return $this->parentRegistry->getBeanDefinition($beanName);
		}
		throw new Bee_Context_NoSuchBeanDefinitionException($beanName);
	}
	
	public function getBeanDefinitionCount() {
		return count($this->beanDefinitions);
	}
	
	public function getBeanDefinitionNames() {
		return array_keys($this->beanDefinitions);
	}
	
	public function registerBeanDefinition($beanName, IBeanDefinition $beanDefinition) {
		if (array_key_exists($beanName, $this->beanDefinitions)) {
			throw new Bee_Context_BeanDefinitionStoreException('Bean name already in use.', $beanName);
		}
		$this->beanDefinitions[$beanName] = $beanDefinition;

        $beanName = $this->canonicalName($beanName);
        if(!array_key_exists($beanName, $this->beanPostProcessorMap)) {
            if(Bee_Utils_Types::isAssignable($beanDefinition->getBeanClassName(), 'Bee\Context\Config\IBeanPostProcessor')) {
                $this->beanPostProcessorMap[$beanName] = true;
                if (Bee_Utils_Types::isAssignable($beanDefinition->getBeanClassName(), 'Bee\Context\Config\IInstantiationAwareBeanPostProcessor')) {
                    $this->hasInstantiationAwareBeanPostProcessors = true;
                }
                if (Bee_Utils_Types::isAssignable($beanDefinition->getBeanClassName(), 'Bee\Context\Config\IDestructionAwareBeanPostProcessor')) {
                    $this->hasDestructionAwareBeanPostProcessors = true;
                }
            }
        }
	}
	
	public function removeBeanDefinition($beanName) {
		if (!array_key_exists($beanName, $this->beanDefinitions)) {
			throw new Bee_Context_NoSuchBeanDefinitionException($beanName);
		}
		unset($this->beanDefinitions[$beanName]);
	}

    /**
     * @param BasicBeanDefinitionRegistry $registry
     * @return void
     */
	protected function getDefinitionsFromRegistry(BasicBeanDefinitionRegistry $registry) {
		$this->getAliasesFromRegistry($registry);
		$this->beanDefinitions = $registry->beanDefinitions;
        $this->beanPostProcessorMap = array_fill_keys($registry->getBeanPostProcessorNames(), true);
        $this->hasInstantiationAwareBeanPostProcessors = $registry->hasInstantiationAwareBeanPostProcessors();
        $this->hasDestructionAwareBeanPostProcessors = $registry->hasDestructionAwareBeanPostProcessors();
//		$this->mergeBeanDefinitions();
	}
	
//	protected function mergeBeanDefinitions() {
//		$beanNames = array_keys($this->beanDefinitions);
//		foreach($beanNames as $beanName) {
//			$this->mergeBeanDefinition($beanName);
//		}
//	}
	
//	protected function mergeBeanDefinition($beanName) {
//		$bd = $this->getBeanDefinition($beanName);
//		$parentName = $bd->getParentName();
//		if(!is_null($parentName)) {
//			$parentName = $this->transformedBeanName($parentName);
//			$parentBd = $this->mergeBeanDefinition($parentName);
//			$newBd = new Bee_Context_Config_BeanDefinition_Generic($parentBd);
//			$newBd->overrideFrom($bd);
//			$newBd->setParentName(null);
//			$bd = $newBd;
//			$this->beanDefinitions[$beanName] = $bd;
//		}
//		return $bd;
//	}
	
    public function getBeanPostProcessorCount() {
        return count($this->beanPostProcessorMap);
    }

    /**
     * Return the list of BeanPostProcessors that will get applied to beans created with this factory.
     *
     * @return IBeanPostProcessor[]
     */
    public function getBeanPostProcessorNames() {
        return array_keys($this->beanPostProcessorMap);
    }

    /**
     * Return whether this factory holds a InstantiationAwareBeanPostProcessor
     * that will get applied to singleton beans on shutdown.
     */
    protected function hasInstantiationAwareBeanPostProcessors() {
        return $this->hasInstantiationAwareBeanPostProcessors;
    }

    /**
     * Return whether this factory holds a DestructionAwareBeanPostProcessor
     * that will get applied to singleton beans on shutdown.
     */
    protected function hasDestructionAwareBeanPostProcessors() {
        return $this->hasDestructionAwareBeanPostProcessors;
    }

	/**
	 * Enter description here...
	 *
	 * @param String $beanName
	 * @return String
	 */
	protected function transformedBeanName($beanName) {
		return $this->canonicalName(ContextUtils::transformedBeanName($beanName));
	}

    /**
     * @access protected
     * @return IBeanDefinition[]
     */
    protected function getBeanDefinitions() {
        return $this->beanDefinitions;
    }

	/**
	 * @param IBeanDefinitionRegistry $parentRegistry
	 */
	public function setParentRegistry(IBeanDefinitionRegistry $parentRegistry) {
		$this->parentRegistry = $parentRegistry;
	}

	/**
	 * @return IBeanDefinitionRegistry
	 */
	public function getParentRegistry() {
		return $this->parentRegistry;
	}
}
