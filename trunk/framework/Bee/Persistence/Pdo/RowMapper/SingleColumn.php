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
 * Date: Mar 24, 2010
 * Time: 8:59:42 AM
 */

class Bee_Persistence_Pdo_RowMapper_SingleColumn implements Bee_Persistence_Pdo_IRowMapper {

    public function mapRow(PDOStatement $rs, $rowNum) {
        $colCount = $rs->columnCount();
        if($colCount != 1) {
            throw new Bee_Persistence_Exception_DataAccess('Incorrect column count, is ' . $colCount . ', should be 1');
        }
        return $rs->fetchColumn();
    }
}
?>
