<?php
namespace Bee\Context\Config\Scope;
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
use Bee\Context\Config\IObjectFactory;
use Bee\Context\Config\IScope;

/**
 * Enter description here...
 *
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class CacheScope implements IScope {
	
	private $id;

	public function __construct($id) {
		$this->id = $id;
	}

	public function &get($beanName, IObjectFactory $objectFactory) {
        return Manager::retrieveCachable(new CachableConfig($this->getCacheKey($beanName), $objectFactory));
	}

	public function remove($beanName) {
		Manager::evict($this->getCacheKey($beanName));
	}

    private function getCacheKey($beanName) {
        return $this->id.'|'.$beanName;
    }

	public function getConversationId() {
		return null;
	}
}

/**
 * Class Bee_Context_Config_Scope_Cache_CachableConfig
 * @package Bee\Context\Config\Scope
 */
class CachableConfig implements ICachableResource {

	/**
	 * @var
	 */
    private $key;

    /**
     * @var IObjectFactory
     */
    private $objectFactory;

	/**
	 * @param $key
	 * @param IObjectFactory $objectFactory
	 */
    public function __construct($key, IObjectFactory $objectFactory) {
        $this->key = $key;
        $this->objectFactory = $objectFactory;
    }

	/**
	 * @return string
	 */
    public function getKey() {
        return $this->key;
    }

	/**
	 * @return int
	 */
    public function getModificationTimestamp() {
        return $this->objectFactory->getModificationTimestamp();
    }

	/**
	 * @param int $expirationTimestamp
	 * @return mixed
	 */
    public function &createContent(&$expirationTimestamp = 0) {
        return $this->objectFactory->getObject();
    }

}
