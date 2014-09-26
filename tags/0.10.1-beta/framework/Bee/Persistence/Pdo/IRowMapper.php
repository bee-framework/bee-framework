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
 * Date: Mar 22, 2010
 * Time: 6:29:04 PM
 */

interface Bee_Persistence_Pdo_IRowMapper {

    /**
     * Implementations must implement this method to map each row of data
     * in the ResultSet. This method should not call <code>next()</code> on
     * the ResultSet; it is only supposed to map values of the current row.
     * @param PDOStatement $rs the PDOStatement to map (pre-initialized for the current row)
     * @param int $rowNum the number of the current row
     * @return mixed the result object for the current row
     * @throws SQLException if a SQLException is encountered getting
     * column values (that is, there's no need to catch SQLException)
     */
    public function mapRow(PDOStatement $rs, $rowNum);

}
?>