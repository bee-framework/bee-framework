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
 * Date: Mar 16, 2010
 * Time: 7:29:55 PM
 */

interface Bee_Persistence_Pdo_IStatementSetter {
    /**
     * Set parameter values on the given PreparedStatement.
     * @param PDOStatement $ps the PreparedStatement to invoke setter methods on
     * @throws PDOException if a PDOException is encountered
     * (i.e. there is no need to catch PDOException)
     */
    public function setValues(PDOStatement $ps);

}
?>