<?php
namespace Bee\MVC;
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
 * Class FilterChainProxy
 * @package Bee\MVC
 */
class FilterChainProxy implements IFilter {

    /**
     * @var IFilter[]
     */
    private $filters;

    /**
     * Gets the Filters
     *
     * @return IFilter[] $filters
     */
    public function getFilters() {
        return $this->filters;
    }

    /**
     * Sets the Filters
     *
     * @param $filters IFilter[]
     * @return void
     */
    public function setFilters($filters) {
        $this->filters = $filters;
    }

    public function doFilter(IHttpRequest $request, IFilterChain $filterChain) {
        if(!$this->filters || count($this->filters) == 0) {
            $filterChain->doFilter($request);
        } else {
            $vfc = new VirtualFilterChain($filterChain, $this->filters);
            $vfc->doFilter($request);
        }
    }

}

class VirtualFilterChain implements IFilterChain {

    /**
     * @var IFilterChain
     */
    private $origFilterChain;

    /**
     * @var IFilter[]
     */
    private $filters;

    /**
     * @var int
     */
    private $currentPosition = 0;

	/**
	 * @param IFilterChain $origFilterChain
	 * @param IFilter[] $filters
	 */
    public function __construct(IFilterChain $origFilterChain, array $filters) {
        $this->origFilterChain = $origFilterChain;
        $this->filters = $filters;
    }

    public function doFilter(IHttpRequest $request) {
        if($this->currentPosition == sizeof($this->filters)) {
            $this->origFilterChain->doFilter($request);
        } else {
            $this->currentPosition++;
            $nextFilter = $this->filters[$this->currentPosition - 1];
            $nextFilter->doFilter($request, $this);
        }
    }
}