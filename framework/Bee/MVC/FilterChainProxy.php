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
 * Date: Dec 18, 2009
 * Time: 5:05:03 PM
 */

class Bee_MVC_FilterChainProxy implements Bee_MVC_IFilter {

    /**
     * @var Bee_MVC_IFilter[]
     */
    private $filters;

    /**
     * Gets the Filters
     *
     * @return Bee_MVC_IFilter[] $filters
     */
    public function getFilters() {
        return $this->filters;
    }

    /**
     * Sets the Filters
     *
     * @param $filters Bee_MVC_IFilter[]
     * @return void
     */
    public function setFilters($filters) {
        $this->filters = $filters;
    }

    public function doFilter(Bee_MVC_IHttpRequest $request, Bee_MVC_IFilterChain $filterChain) {
        if(!$this->filters || count($this->filters) == 0) {
            $filterChain->doFilter($request);
        } else {
            $vfc = new Bee_MVC_VirtualFilterChain($filterChain, $this->filters);
            $vfc->doFilter($request);
        }
    }

}

class Bee_MVC_VirtualFilterChain implements Bee_MVC_IFilterChain {

    /**
     * @var Bee_MVC_IFilterChain
     */
    private $origFilterChain;

    /**
     * @var Bee_MVC_IFilter[]
     */
    private $filters;

    /**
     * @var int
     */
    private $currentPosition = 0;

    /**
     * @param Bee_MVC_IFilterChain $origFilterChain
     * @param Bee_MVC_IFilter[] $filters
     * @return void
     */
    public function __construct(Bee_MVC_IFilterChain $origFilterChain, array $filters) {
        $this->origFilterChain = $origFilterChain;
        $this->filters = $filters;
    }
    public function doFilter(Bee_MVC_IHttpRequest $request) {
        if($this->currentPosition == sizeof($this->filters)) {
            $this->origFilterChain->doFilter($request);
        } else {
            $this->currentPosition++;
            $nextFilter = $this->filters[$this->currentPosition - 1];
            $nextFilter->doFilter($request, $this);
        }
    }

}
?>