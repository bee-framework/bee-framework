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
 * Time: 7:17:05 PM
 */

interface Bee_Persistence_Pdo_IStatementCreator {

    /** 
     * Create a statement in this connection. Allows implementations to use
     * PreparedStatements. The JdbcTemplate will close the created statement.
     * @param PDO $con Connection to use to create statement
     * @return PDOStatement a prepared statement
     * @throws PDOException there is no need to catch PDOException
     * that may be thrown in the implementation of this method.
     * The PDOTemplate class will handle them.
     */
    public function createStatement(PDO $con);

}
?>