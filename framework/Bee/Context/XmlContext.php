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
use Bee\Cache\ICachableResource;
use Bee\Cache\Manager;
use Bee\Context\Config\BasicBeanDefinitionRegistry;
use Bee\Context\Xml\BeanDefinitionReader;
use Bee\Framework;
use Exception;

/**
 * Enter description here...
 * 
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class XmlContext extends AbstractContext {

    /**
     * @var Bee_Context_Xml_CachableConfig
     */
    private $configCacheable;

	/**
	 * Enter description here...
	 *
	 * @param String $locations
	 * @param bool $callInitMethod
	 * @return XmlContext
	 */
	public function __construct($locations='', $callInitMethod=true) {
		parent::__construct($locations, false);
        $this->configCacheable = new Bee_Context_Xml_CachableConfig(explode(",", $locations));
		if ($callInitMethod) {
			$this->init();
		}
	}

	protected function loadBeanDefinitions() {
		$registry = Manager::retrieveCachable($this->configCacheable);
		$this->getDefinitionsFromRegistry($registry);
	}	

    public function getModificationTimestamp() {
        return $this->configCacheable->getModificationTimestamp();
    }
}

class Bee_Context_Xml_CachableConfig implements ICachableResource {
	
	/**
	 * Enter description here...
	 *
	 * @var ICachableResource
	 */
	private $parentConfig;
	
	/**
	 * Enter description here...
	 *
	 * @var array
	 */
	private $configLocations;

    private $modificationTimestamp = false;

	public function __construct(array $configLocations, ICachableResource $parentConfig = null) {
		$this->configLocations = $configLocations;
		$this->parentConfig = $parentConfig;
	}
	
	public function getKey() {
		return implode('|', $this->configLocations);
	}
	
	public function getModificationTimestamp() {
		if(Framework::getProductionMode()) {
			return 0;
		}
        if($this->modificationTimestamp === false) {
            $latest = is_null($this->parentConfig) ? 0 : $this->parentConfig->getModificationTimestamp();
            foreach($this->configLocations as $location) {
                if (!file_exists($location)) {
                    throw new Exception("File '$location' does not exist");
                }

				$latest = max(array(filectime($location), filemtime($location), $latest));
            }
            $this->modificationTimestamp = $latest;
        }
		return $this->modificationTimestamp;
	}
	
	public function &createContent(&$expirationTimestamp = 0) {
		$registry = new BasicBeanDefinitionRegistry();
		$reader = new BeanDefinitionReader($registry);
		
		foreach($this->configLocations as $location) {
			if (!file_exists($location)) {
                throw new Exception("File '$location' does not exist");
			} else {
				$reader->loadBeanDefinitions($location);
			}
		}
		
		return $registry;
	}
}