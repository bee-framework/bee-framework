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
class Bee_Context_Config_Scope_Cache implements Bee_Context_Config_IScope {
	
	private $id;

	public function __construct($id) {
		$this->id = $id;
	}

	public function get($beanName, Bee_Context_Config_IObjectFactory $objectFactory) {
        return Bee_Cache_Manager::retrieveCachable(new Bee_Context_Config_Scope_Cache_CachableConfig($this->getCacheKey(), $objectFactory));
	}

	public function remove($beanName) {
        Bee_Cache_Manager::evict($this->getCacheKey($beanName));
	}

    private function getCacheKey($beanName) {
        return $this->id.'|'.$beanName;
    }

	public function getConversationId() {
		return null;
	}
}

class Bee_Context_Config_Scope_Cache_CachableConfig implements Bee_Cache_ICachableResource {

    private $key;

    /**
     * @var Bee_Context_Config_IObjectFactory 
     */
    private $objectFactory;

    public function __construct($key, Bee_Context_Config_IObjectFactory $objectFactory) {
        $this->key = $key;
        $this->objectFactory = $objectFactory;
    }

    public function getKey() {
        return $this->key;
    }

    public function getModificationTimestamp() {
        return $this->objectFactory->getModificationTimestamp();
    }

    public function &createContent(&$expirationTimestamp = 0) {
        return $this->objectFactory->getObject();
    }

}
?>