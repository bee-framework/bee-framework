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
 * Time: 7:37:44 PM
 */

interface Bee_Persistence_Pdo_IStatementCallback {
    /**
     * Gets called by <code>PDOTemplate.execute</code> with an active PDOStatement.
     * Does not need to care about closing the Statement
     * or the Connection, or about handling transactions: this will all be
     * handled by Bee's PDOTemplate.
     *
     * <p><b>NOTE:</b> Any ResultSets opened should be closed in finally blocks
     * within the callback implementation. Spring will close the Statement
     * object after the callback returned, but this does not necessarily imply
     * that the ResultSet resources will be closed: the Statement objects might
     * get pooled by the connection pool, with <code>close</code> calls only
     * returning the object to the pool but not physically closing the resources.
     *
     * <p>Allows for returning a result object created within the callback, i.e.
     * a domain object or a collection of domain objects. Note that there's
     * special support for single step actions: see JdbcTemplate.queryForObject etc.
     * A thrown RuntimeException is treated as application exception, it gets
     * propagated to the caller of the template.
     *
     * @param ps active JDBC PreparedStatement
     * @return a result object, or <code>null</code> if none
     * @throws PDOException
     * @throws Bee_Persistence_Exception_DataAccess in case of custom exceptions
     */
    public function doInPreparedStatement(PDOStatement $ps);

}
