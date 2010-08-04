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

class Bee_Context_Config_BasicBeanDefinitionRegistry extends Bee_Context_AliasRegistry implements Bee_Context_Config_IBeanDefinitionRegistry {
	
	/**
	 * Enter description here...
	 *
	 * @var Bee_Context_Config_IBeanDefinition[]
	 */
	private $beanDefinitions = array();
	
    /**
     * BeanPostProcessors to apply in createBean
     * @var Bee_Context_Config_IBeanPostProcessor[]
     */
	private $beanPostProcessorNames = null;

    /**
     *
     * @var boolean
     */
    private $hasInstantiationAwareBeanPostProcessors;

    /**
     * @var boolean
     */
    private $hasDestructionAwareBeanPostProcessors;
	
	public function containsBeanDefinition($beanName) {
		return array_key_exists($beanName, $this->beanDefinitions);
	}

    /**
     * @throws Bee_Context_NoSuchBeanDefinitionException
     * @param  $beanName
     * @return Bee_Context_Config_IBeanDefinition
     */
	public function getBeanDefinition($beanName) {
		if (array_key_exists($beanName, $this->beanDefinitions)) {
			return $this->beanDefinitions[$beanName];
		}
		throw new Bee_Context_NoSuchBeanDefinitionException($beanName);
	}
	
	public function getBeanDefinitionCount() {
		return count($this->beanDefinitions);
	}
	
	public function getBeanDefinitionNames() {
		return array_keys($this->beanDefinitions);
	}
	
	public function registerBeanDefinition($beanName, Bee_Context_Config_IBeanDefinition $beanDefinition) {
		if (array_key_exists($beanName, $this->beanDefinitions)) {
			throw new Bee_Context_BeanDefinitionStoreException('Bean name already in use.', $beanName);
		}
		$this->beanDefinitions[$beanName] = $beanDefinition;

        $beanName = $this->canonicalName($beanName);
        if(!in_array($beanName, $this->beanPostProcessorNames)) {
            if(Bee_Utils_Types::isAssignable($beanDefinition->getBeanClassName(), 'Bee_Context_Config_IBeanPostProcessor')) {
                $this->beanPostProcessorNames[] = $beanName;
                if (Bee_Utils_Types::isAssignable($beanDefinition->getBeanClassName(), 'Bee_Context_Config_IInstantiationAwareBeanPostProcessor')) {
                    $this->hasInstantiationAwareBeanPostProcessors = true;
                }
                if (Bee_Utils_Types::isAssignable($beanDefinition->getBeanClassName(), 'Bee_Context_Config_IDestructionAwareBeanPostProcessor')) {
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
     * @param Bee_Context_Config_BasicBeanDefinitionRegistry $registry
     * @return void
     */
	public function getDefinitionsFromRegistry(Bee_Context_Config_BasicBeanDefinitionRegistry $registry) {
		$this->getAliasesFromRegistry($registry);
		$this->beanDefinitions = $registry->beanDefinitions;
        $this->beanPostProcessorNames = $registry->getBeanPostProcessorNames();
        $this->hasInstantiationAwareBeanPostProcessors = $registry->hasInstantiationAwareBeanPostProcessors();
        $this->hasDestructionAwareBeanPostProcessors = $registry->hasDestructionAwareBeanPostProcessors();
		$this->mergeBeanDefinitions();
	}
	
	protected function mergeBeanDefinitions() {
		$beanNames = array_keys($this->beanDefinitions);
		foreach($beanNames as $beanName) {
			$this->mergeBeanDefinition($beanName);
		}
	}
	
	protected function mergeBeanDefinition($beanName) {
		$bd = $this->getBeanDefinition($beanName);
		$parentName = $bd->getParentName();
		if(!is_null($parentName)) {
			$parentName = $this->transformedBeanName($parentName);
			$parentBd = $this->mergeBeanDefinition($parentName);
			$newBd = new Bee_Context_Config_BeanDefinition_Generic($parentBd);
			$newBd->overrideFrom($bd);
			$newBd->setParentName(null);
			$bd = $newBd;
			$this->beanDefinitions[$beanName] = $bd;
		}
		return $bd;
	}
	
    public function getBeanPostProcessorCount() {
        return count($this->beanPostProcessorNames);
    }

    /**
     * Return the list of BeanPostProcessors that will get applied to beans created with this factory.
     *
     * @return Bee_Context_Config_IBeanPostProcessor[]
     */
    public function getBeanPostProcessorNames() {
        return $this->beanPostProcessorNames;
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
		return $this->canonicalName(Bee_Context_Support_ContextUtils::transformedBeanName($beanName));
	}

    /**
     * @access protected
     * @return Bee_Context_Config_IBeanDefinition[]
     */
    protected function getBeanDefinitions() {
        return $this->beanDefinitions;
    }

}
?>