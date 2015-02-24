<?php
/*
 * Copyright 2008-2015 the original author or authors.
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
namespace Bee\MVC\View;


use Bee\MVC\Redirect\AbstractRedirectStorage;

/**
 * Class TRequestStoringView
 * @package Bee\MVC\View
 */
trait TRequestStoringView {

    /**
     * @var string
     */
    private $requestIdParamName = 'requestId';

    /**
     * @var AbstractRedirectStorage[]
     */
    private $stores = array();

    /**
     * @param $model
     * @return array
     */
    protected function createStoreParams($model) {
        $this->augmentModel($model);
        $storeParams = array();
        foreach($this->stores as $paramName => $store) {
            $storeParams[$paramName] = $store->storeData($model);
        }
        return $storeParams;
    }

    /**
     * Extension point for subclasses
     * @param array $model
     */
    protected function augmentModel(array &$model) {
        // do nothing by default
    }

    // =================================================================================================================
    // == GETTERS & SETTERS ============================================================================================
    // =================================================================================================================

    /**
     * @param string $requestIdParamName
     */
    public function setRequestIdParamName($requestIdParamName) {
        $this->requestIdParamName = $requestIdParamName;
    }

    /**
     * @return string
     */
    public function getRequestIdParamName() {
        return $this->requestIdParamName;
    }

    /**
     * @param array $stores
     */
    public function setStores(array $stores) {
        $this->stores = $stores;
    }

    /**
     * @return array
     */
    public function getStores() {
        return $this->stores;
    }
}