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
 * Time: 6:01:23 PM
 */

interface Bee_Persistence_Pdo_IBatchStatementSetter {

    /**
     * Set parameter values on the given PDOStatement.
     * @param PDOStatement $ps the PreparedStatement to invoke setter methods on
     * @param int $i index of the statement we're issuing in the batch, starting from 0
     * @throws PDOException if a PDOException is encountered
     */
    public function setValues(PDOStatement $ps, $i);

    /**
     * Return the size of the batch.
     * @return int the number of statements in the batch
     */
    public function getBatchSize();

}
?>