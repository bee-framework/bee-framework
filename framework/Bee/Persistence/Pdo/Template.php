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
 * Time: 7:15:00 PM
 */

class Bee_Persistence_Pdo_Template {

    /**
     * @var PDO
     */
    private $pdoConnection;

    /**
     * Gets the PdoConnection
     *
     * @return PDO $pdoConnection
     */
    public function getPdoConnection() {
        return $this->pdoConnection;
    }

    /**
     * Sets the PdoConnection
     *
     * @param $pdoConnection PDO
     * @return void
     */
    public function setPdoConnection(PDO $pdoConnection) {
        $this->pdoConnection = $pdoConnection;
    }

    public function __construct(PDO $pdoConnection) {
        $this->pdoConnection = $pdoConnection;
    }

    public function queryBySqlString($sql, Bee_Persistence_Pdo_IStatementSetter $pss,
                                     Bee_Persistence_Pdo_IResultSetExtractor $rse) {
        return $this->query(new Bee_Persistence_Pdo_SimpleStatementCreator($sql), $pss, $rse);
    }

    public function queryColumnBySqlStringAndArgsArray($sql, array $args) {
        return $this->query(new Bee_Persistence_Pdo_SimpleStatementCreator($sql),
            new Bee_Persistence_Pdo_StatementSetter_Args($args),
            new Bee_Persistence_Pdo_ResultSetExtractor_SingleColumn());
    }

    public function queryScalarBySqlStringAndArgsArray($sql, array $args) {
        $results = $this->query(new Bee_Persistence_Pdo_SimpleStatementCreator($sql),
            new Bee_Persistence_Pdo_StatementSetter_Args($args),
            new Bee_Persistence_Pdo_ResultSetExtractor_RowMapper(
            new Bee_Persistence_Pdo_RowMapper_SingleColumn()));
        $count = count($results);
        if($count != 1) {
            throw new Bee_Persistence_Exception_DataAccess('Incorrect result size, is ' .$count. ', expected 1');
        }
        return $results[0];
    }

    /**
     * Query using a prepared statement, allowing for a IStatementCreator
     * and a IStatementSetter. Most other query methods use this method,
     * but application code will always work with either a creator or a setter.
     * @param psc Callback handler that can create a PreparedStatement given a
     * Connection
     * @param pss object that knows how to set values on the prepared statement.
     * If this is null, the SQL will be assumed to contain no bind parameters.
     * @param rse object that will extract results.
     * @return an arbitrary result object, as returned by the ResultSetExtractor
     * @throws DataAccessException if there is any problem
     */
    public function query(Bee_Persistence_Pdo_IStatementCreator $psc, Bee_Persistence_Pdo_IStatementSetter $pss,
                          Bee_Persistence_Pdo_IResultSetExtractor $rse) {

        Bee_Utils_Assert::notNull($rse, 'ResultSetExtractor must not be null');
        Bee_Utils_Logger::debug('Executing prepared SQL query');

        return $this->execute($psc, new Bee_Persistence_Pdo_Template_QueryCallback($pss, $rse));
    }

    public function updateBySqlStringAndArgsArray($sql, array $args) {
        return $this->updateBySqlString($sql, new Bee_Persistence_Pdo_StatementSetter_Args($args));
    }

    public function updateBySqlString($sql, Bee_Persistence_Pdo_IStatementSetter $pss) {
        return $this->update(new Bee_Persistence_Pdo_SimpleStatementCreator($sql), $pss);
    }

    public function update(Bee_Persistence_Pdo_IStatementCreator $psc, Bee_Persistence_Pdo_IStatementSetter $pss) {
        Bee_Utils_Logger::debug('Executing prepared SQL update');
        return $this->execute($psc, new Bee_Persistence_Pdo_Template_UpdateCallback($pss));
    }

    public function batchUpdateBySqlString($sql, Bee_Persistence_Pdo_IBatchStatementSetter $bss) {
        return $this->batchUpdate(new Bee_Persistence_Pdo_SimpleStatementCreator($sql), $bss);
    }

    public function batchUpdate(Bee_Persistence_Pdo_IStatementCreator $psc, Bee_Persistence_Pdo_IBatchStatementSetter $bss) {
        Bee_Utils_Logger::debug('Executing prepared SQL batch update');
        return $this->execute($psc, new Bee_Persistence_Pdo_Template_BatchUpdateCallback($bss));
    }

    //-------------------------------------------------------------------------
    // Methods dealing with prepared statements
    //-------------------------------------------------------------------------

    public function execute(Bee_Persistence_Pdo_IStatementCreator $psc, Bee_Persistence_Pdo_IStatementCallback $action) {

        Bee_Utils_Assert::notNull($psc, 'PreparedStatementCreator must not be null');
        Bee_Utils_Assert::notNull($action, 'Callback object must not be null');

        if (Bee_Utils_Logger::isDebugEnabled()) {
            $sql = self::getSql($psc);
            Bee_Utils_Logger::debug('Executing prepared SQL statement' + (!is_null($sql) ? " [" + $sql + "]" : ""));
        }

        $ps = null;
        try {
            $ps = $psc->createStatement($this->pdoConnection);
            $result = $action->doInPreparedStatement($ps);
            return $result;
        }
        catch (PDOException $ex) {
            // Release Connection early, to avoid potential connection pool deadlock
            // in the case when the exception translator hasn't been initialized yet.
//            $sql = $this->getSql($psc);
            throw new Bee_Persistence_Exception_DataAccess('Bee_Persistence_Pdo_Template caught an exception', $ex);
//            throw getExceptionTranslator().translate("PreparedStatementCallback", sql, ex);
        }
    }

    private static function getSql($sqlProvider) {
        if ($sqlProvider instanceof Bee_Persistence_Pdo_ISqlProvider) {
            return $sqlProvider->getSql();
        }
        else {
            return null;
        }
    }

}

class Bee_Persistence_Pdo_Template_QueryCallback implements Bee_Persistence_Pdo_IStatementCallback {

    /**
     * @var Bee_Persistence_Pdo_IStatementSetter
     */
    private $pss;

    /**
     * @var Bee_Persistence_Pdo_IResultSetExtractor
     */
    private $rse;

    public function __construct(Bee_Persistence_Pdo_IStatementSetter $pss, Bee_Persistence_Pdo_IResultSetExtractor $rse) {
        $this->pss = $pss;
        $this->rse = $rse;
    }

    function doInPreparedStatement(PDOStatement $ps) {
        try {
            if ($this->pss != null) {
                $this->pss->setValues($ps);
            }
            $ps->execute();
            return $this->rse->extractData($ps);
        } catch(Exception $e) {
            $ps->closeCursor();
            throw $e;
        }
        $ps->closeCursor();
    }

}

class Bee_Persistence_Pdo_Template_UpdateCallback implements Bee_Persistence_Pdo_IStatementCallback {

    /**
     * @var Bee_Persistence_Pdo_IStatementSetter
     */
    private $pss;

    public function __construct(Bee_Persistence_Pdo_IStatementSetter $pss) {
        $this->pss = $pss;
    }

    function doInPreparedStatement(PDOStatement $ps) {
        if ($this->pss != null) {
            $this->pss->setValues($ps);
        }
        if($ps->execute()) {
            $rows = $ps->rowCount();
            if(Bee_Utils_Logger::isDebugEnabled()) {
                Bee_Utils_Logger::debug('SQL update affected '.$rows.' rows');
            }
            return $rows;
        }
        return false;
    }
}

class Bee_Persistence_Pdo_Template_BatchUpdateCallback implements Bee_Persistence_Pdo_IStatementCallback {

    /**
     * @var Bee_Persistence_Pdo_IBatchStatementSetter
     */
    private $bss;

    public function __construct(Bee_Persistence_Pdo_IBatchStatementSetter $bss) {
        $this->bss = $bss;
    }

    function doInPreparedStatement(PDOStatement $ps) {
        $batchSize = $this->bss->getBatchSize();
        $rowsAffected = array();
        for ($i = 0; $i < $batchSize; $i++) {
            $this->bss->setValues($ps, $i);
            if($ps->execute()) {
                $rowsAffected[$i] = $ps->rowCount();
            } else {
                throw new PDOException($ps->errorInfo(), $ps->errorCode());
            }
        }
        return $rowsAffected;
    }
}
?>