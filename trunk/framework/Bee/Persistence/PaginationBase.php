<?php
namespace Bee\Persistence;
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
use ArrayAccess;
use JsonSerializable;

/**
 * Class BasicListStateHolder
 *
 * Implements the pagination aspect of the IOrderAndLimitHolder and embodies generic logic for
 * @package Bee\Persistence
 */
abstract class PaginationBase implements IOrderAndLimitHolder, JsonSerializable {

    /**
     * @var ArrayAccess | array
     */
    private $state = array();

    /**
     *
     */
    function __construct() {
        $this->setPageSize(50);
    }


    /**
	 * @return int
	 */
	public function getPageSize() {
        return $this->getIndexSave('pageSize');
	}

	/**
	 * @param int $pageSize
	 */
	public function setPageSize($pageSize) {
		$this->state['pageSize'] = intval($pageSize);
	}

	/**
	 * @return int
	 */
	public function getPageCount() {
		return ceil($this->getResultCount() / $this->getPageSize());
	}

	/**
	 * @return int
	 */
	public function getCurrentPage() {
        return $this->getIndexSave('currentPage');
	}

	/**
	 * @param $currentPage
	 */
	public function setCurrentPage($currentPage) {
        $currentPage = intval($currentPage);
		$this->state['currentPage'] = $currentPage < 0 ? 0 : $currentPage;
	}

	/**
	 * @param int $resultCount
	 */
	public function setResultCount($resultCount) {
		$this->state['resultCount'] = intval($resultCount);
		if($this->getCurrentPage() >= $this->getPageCount()) {
            $this->adjustCurrentPageOnOverflow();
		}
	}

	/**
	 * @return int
	 */
	public function getResultCount() {
        return $this->getIndexSave('resultCount');
	}

	/**
	 * Implements the default behavior in case the current page is beyond the acceptable limit. By default, sets the
	 * current page to the last page.
	 */
	protected function adjustCurrentPageOnOverflow() {
		$this->setCurrentPage($this->getPageCount() - 1);
	}

    /**
     * @return array|ArrayAccess
     */
    public function getState() {
        return $this->state;
    }

    /**
     * @param array|ArrayAccess $state
     */
    public function setState($state) {
        $this->state = $state;
    }

    /**
     * @return array
     */
    function jsonSerialize() {
        return array('pageCount' => $this->getPageCount(), 'pageSize' => $this->getPageSize(), 'currentPage' => $this->getCurrentPage(), 'resultCount' => $this->getResultCount());
    }

    private function getIndexSave($idx, $defaultVal = 0) {
        return array_key_exists($idx, $this->state) ? $this->state[$idx] : $defaultVal;
    }
}