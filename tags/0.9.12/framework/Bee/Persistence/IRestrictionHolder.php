<?php

interface Bee_Persistence_IRestrictionHolder {

    /**
     * @return array
     */
    public function getFilterableFields();

    /**
     * @return array
     */
    public function getFilterString();

    /**
     * @return array
     */
    public function getFieldRestrictions();

}

?>