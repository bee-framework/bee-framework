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
use Bee\Persistence\Exception\DataAccessException;

/**
 * User: mp
 * Date: Mar 16, 2010
 * Time: 7:32:38 PM
 */

interface Bee_Persistence_Pdo_IResultSetExtractor {
    /**
     * Implementations must implement this method to process the entire ResultSet.
     * @param PDOStatement $rs PDOStatement to extract data from. Implementations should
     * not close this: it will be closed by the calling JdbcTemplate.
     * @return an arbitrary result object, or <code>null</code> if none
     * (the extractor will typically be stateful in the latter case).
     * @throws PDOException if a PDOException is encountered getting column
     * values or navigating (that is, there's no need to catch PDOException)
     * @throws DataAccessException in case of custom exceptions
     */
    public function extractData(PDOStatement $rs);
}