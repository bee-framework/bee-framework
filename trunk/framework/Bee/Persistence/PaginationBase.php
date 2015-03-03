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
use JsonSerializable;

/**
 * Class BasicListStateHolder
 *
 * Implements the pagination aspect of the IOrderAndLimitHolder and embodies generic logic for
 * @package Bee\Persistence
 */
abstract class PaginationBase implements IOrderAndLimitHolder, JsonSerializable {

	/**
	 * @var int
	 */
	private $pageSize = 50;

	/**
	 * @var int
	 */
	private $currentPage = 0;

	/**
	 * @var int
	 */
	private $resultCount;

	/**
	 * @return int
	 */
	public function getPageSize() {
		return $this->pageSize;
	}

	/**
	 * @param int $pageSize
	 */
	public function setPageSize($pageSize) {
		$this->pageSize = intval($pageSize);
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
		return $this->currentPage;
	}

	/**
	 * @param $currentPage
	 */
	public function setCurrentPage($currentPage) {
        $currentPage = intval($currentPage);
		$this->currentPage = $currentPage < 0 ? 0 : $currentPage;
	}

	/**
	 * @param int $resultCount
	 */
	public function setResultCount($resultCount) {
		$this->resultCount = intval($resultCount);
		if($this->getCurrentPage() >= $this->getPageCount()) {
            $this->adjustCurrentPageOnOverflow();
		}
	}

	/**
	 * @return int
	 */
	public function getResultCount() {
		return $this->resultCount;
	}

	/**
	 * Implements the default behavior in case the current page is beyond the acceptable limit. By default, sets the
	 * current page to the last page.
	 */
	protected function adjustCurrentPageOnOverflow() {
		$this->setCurrentPage($this->getPageCount() - 1);
	}

    /**
     * @return array
     */
    function jsonSerialize() {
        return array('pageCount' => $this->getPageCount(), 'pageSize' => $this->getPageSize(), 'currentPage' => $this->getCurrentPage(), 'resultCount' => $this->getResultCount());
    }
}