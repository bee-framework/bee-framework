<?php

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
     * @param $pageCount
     */
    public function setPageCount($pageCount);

    /**
     * @return int
     */
    public function getCurrentPage();

    /**
     * @param $currentPage
     */
    public function setCurrentPage($currentPage);

}

?>