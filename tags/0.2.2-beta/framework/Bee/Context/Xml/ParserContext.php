<?php
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
use Bee\Context\Config\CompositeComponentDefinition;
use Bee\Context\Config\IBeanDefinition;
use Bee\Context\Config\IComponentDefinition;

/**
 * Enter description here...
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class Bee_Context_Xml_ParserContext {
	
	/**
	 * Enter description here...
	 *
	 * @var Bee_Context_Xml_ReaderContext
	 */
	private $readerContext;
	
	
	/**
	 * Enter description here...
	 *
	 * @var Bee_Context_Xml_ParserDelegate
	 */
	private $delegate;
	
	
	/**
	 * Enter description here...
	 *
	 * @var IBeanDefinition
	 */
	private $containingBeanDefinition;
	
	
	/**
	 * Enter description here...
	 *
	 * @var CompositeComponentDefinition[]
	 */
	private $containingComponents = array();

	/**
	 * @param Bee_Context_Xml_ReaderContext $readerContext
	 * @param Bee_Context_Xml_ParserDelegate $delegate
	 * @param IBeanDefinition $containingBeanDefinition
	 */
	public function __construct(
		Bee_Context_Xml_ReaderContext $readerContext, Bee_Context_Xml_ParserDelegate $delegate,
		IBeanDefinition $containingBeanDefinition = null) {
		
		$this->readerContext = $readerContext;
		$this->delegate = $delegate;
		$this->containingBeanDefinition = $containingBeanDefinition;
	}
	
	
	/**
	 * Enter description here...
	 *
	 * @return Bee_Context_Xml_ReaderContext
	 */
	public final function getReaderContext() {
		return $this->readerContext; 
	}
	
	
	/**
	 * Enter description here...
	 *
	 * @return Bee_Context_Xml_ParserDelegate
	 */
	public final function getDelegate() {
		return $this->delegate;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return IBeanDefinition
	 */
	public final function getContainingBeanDefinition() {
		return $this->containingBeanDefinition;
	}

    /**
     * Enter description here...
     *
     * @return Bee\Context\Config\IBeanDefinitionRegistry
     */
    public final function getRegistry() {
        return $this->readerContext->getRegistry();
    }

    /**
     * @return boolean
     */
    public final function isNested() {
        return ($this->containingBeanDefinition != null);
    }

    /**
     * @return CompositeComponentDefinition
     */
    public function getContainingComponent() {
        $count = count($this->containingComponents);
        return $count == 0 ? null : $this->containingComponents[$count-1];
    }

    /**
     * @param CompositeComponentDefinition $containingComponent
     * @return void
     */
    public function pushContainingComponent(CompositeComponentDefinition $containingComponent) {
        array_push($this->containingComponents, $containingComponent);
    }

    /**
     * @return CompositeComponentDefinition
     */
    public function popContainingComponent() {
        return array_pop($this->containingComponents);
    }

    public function popAndRegisterContainingComponent() {
        $this->registerComponent($this->popContainingComponent());
    }

    public function registerComponent(IComponentDefinition $component) {
        $containingComponent = $this->getContainingComponent();
        if ($containingComponent != null) {
            $containingComponent->addNestedComponent($component);
        }
        else {
            throw new Exception("NOT IMPLEMENTED");
//            $this->readerContext->fireComponentRegistered(component);
        }
    }
}