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
 * Date: Mar 17, 2010
 * Time: 10:27:35 AM
 */

class Bee_Persistence_Pdo_SimpleStatementCreator implements Bee_Persistence_Pdo_IStatementCreator, Bee_Persistence_Pdo_ISqlProvider {

    /**
     * @var string
     */
    private $sql;

    /**
     * @param string $sql
     * @return void
     */
    final function __construct($sql) {
        $this->sql = $sql;
    }

    /**
     * Gets the Sql
     *
     * @return  $sql
     */
    public function getSql() {
        return $this->sql;
    }

    public function createStatement(PDO $con) {
        return $con->prepare($this->sql);
    }

}
?>