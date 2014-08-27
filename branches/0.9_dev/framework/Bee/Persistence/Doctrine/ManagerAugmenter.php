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
 * User: mp
 * Date: Jan 15, 2010
 * Time: 3:56:40 PM
 */

class Bee_Persistence_Doctrine_ManagerAugmenter implements Bee_Context_Config_IInitializingBean {

    /**
     * @var Doctrine_Manager
     */
    private $doctrineManager;

    /**
     * @var Doctrine_Cache_Interface
     */
    private $queryCacheDriver;

    /**
     * @var Doctrine_Cache_Interface
     */
    private $resultCacheDriver;

    /**
     * Gets the DoctrineManager
     *
     * @return Doctrine_Manager $doctrineManager
     */
    public function getDoctrineManager() {
        return $this->doctrineManager;
    }

    /**
     * Sets the DoctrineManager
     *
     * @param $doctrineManager Doctrine_Manager
     * @return void
     */
    public function setDoctrineManager(Doctrine_Manager $doctrineManager) {
        $this->doctrineManager = $doctrineManager;
    }

    /**
     * Gets the QueryCacheDriver
     *
     * @return Doctrine_Cache_Interface $queryCacheDriver
     */
    public function getQueryCacheDriver() {
        return $this->queryCacheDriver;
    }

    /**
     * Sets the QueryCacheDriver
     *
     * @param $queryCacheDriver Doctrine_Cache_Interface
     * @return void
     */
    public function setQueryCacheDriver(Doctrine_Cache_Interface $queryCacheDriver) {
        $this->queryCacheDriver = $queryCacheDriver;
    }

    /**
     * Gets the ResultCacheDriver
     *
     * @return Doctrine_Cache_Interface $resultCacheDriver
     */
    public function getResultCacheDriver() {
        return $this->resultCacheDriver;
    }

    /**
     * Sets the ResultCacheDriver
     *
     * @param $resultCacheDriver Doctrine_Cache_Interface
     * @return void
     */
    public function setResultCacheDriver(Doctrine_Cache_Interface $resultCacheDriver) {
        $this->resultCacheDriver = $resultCacheDriver;
    }

    public function afterPropertiesSet() {
//		$this->doctrineManager->setAttribute(Doctrine_Core::ATTR_USE_DQL_CALLBACKS, true);
//		$this->doctrineManager->setAttribute(Doctrine_Core::ATTR_AUTO_FREE_QUERY_OBJECTS, true);
//		$this->doctrineManager->setAttribute(Doctrine_Core::ATTR_QUOTE_IDENTIFIER, true);

		if($this->queryCacheDriver) {
			$this->doctrineManager->setAttribute(Doctrine_Core::ATTR_QUERY_CACHE, $this->queryCacheDriver);
		}

		if($this->resultCacheDriver) {
			$this->doctrineManager->setAttribute(Doctrine_Core::ATTR_RESULT_CACHE, $this->resultCacheDriver);
		}
	}
}
?>
