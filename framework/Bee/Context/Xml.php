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

/**
 * Enter description here...
 * 
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class Bee_Context_Xml extends Bee_Context_Abstract {

    /**
     * @var Bee_Context_Xml_CachableConfig
     */
    private $configCacheable;

	/**
	 * Enter description here...
	 *
	 * @param String $locations
	 * @return void
	 */
	public function __construct($locations='', $callInitMethod=true) {
		parent::__construct($locations, false);
        $this->configCacheable = new Bee_Context_Xml_CachableConfig(explode(",", $locations));
		if ($callInitMethod) {
			$this->init();
		}
	}

	protected function loadBeanDefinitions() {
		$registry = Bee_Cache_Manager::retrieveCachable($this->configCacheable);
		$this->getDefinitionsFromRegistry($registry);
	}	

    public function getModificationTimestamp() {
        return $this->configCacheable->getModificationTimestamp();
    }
}

class Bee_Context_Xml_CachableConfig implements Bee_Cache_ICachableResource {
	
	/**
	 * Enter description here...
	 *
	 * @var Bee_Cache_ICachableResource
	 */
	private $parentConfig;
	
	/**
	 * Enter description here...
	 *
	 * @var array
	 */
	private $configLocations;

    private $modificationTimestamp = false;

	public function __construct(array $configLocations, Bee_Cache_ICachableResource $parentConfig = null) {
		$this->configLocations = $configLocations;
		$this->parentConfig = $parentConfig;
	}
	
	public function getKey() {
		return implode('|', $this->configLocations);
	}
	
	public function getModificationTimestamp() {
		if(Bee_Framework::getProductionMode()) {
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
		$registry = new Bee_Context_Config_BasicBeanDefinitionRegistry(); 
		$reader = new Bee_Context_Xml_BeanDefinitionReader($registry);
		
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
?>