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

/**
 * Interface Bee_Persistence_IOrderAndLimitHolder
 */
interface Bee_Persistence_IOrderAndLimitHolder {

    /**
     * @return array
     */
    public function getOrderMapping();

    /**
     * @return int
     */
    public function getPageSize();

    /**
     * @return int
     */
    public function getPageCount();

    /**
     * @return int
     */
    public function getCurrentPage();

    /**
     * @param $currentPage
     */
    public function setCurrentPage($currentPage);

	/**
	 * @param int $resultCount
	 */
	public function setResultCount($resultCount);
}
