<?php
namespace Bee\Persistence\Pdo;

/*
 * Copyright 2008-2015 the original author or authors.
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
use Bee\Persistence\Pdo\ResultSetExtractor\RowMapperResultSetExtractor;
use Bee\Persistence\Pdo\ResultSetExtractor\SingleColumnResultSetExtractor;
use Bee\Persistence\Pdo\RowMapper\SingleColumnRowMapper;
use Bee\Persistence\Pdo\StatementSetter\ArgsStatementSetter;
use Bee\Utils\Assert;
use Bee\Utils\TLogged;
use Exception;
use PDO;
use PDOException;
use PDOStatement;

/**
 * User: mp
 * Date: Mar 16, 2010
 * Time: 7:15:00 PM
 */
class Template {
    use TLogged;

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

    /**
     * @param $sql
     * @param callable $pss
     * @param callable $rse
     * @return mixed
     */
    public function queryBySqlString($sql, callable $pss, callable $rse) {
        return $this->query(new SimpleStatementCreator($sql), $pss, $rse);
    }

    public function queryColumnBySqlStringAndArgsArray($sql, array $args, array $argTypes = null) {
        return $this->query(new SimpleStatementCreator($sql),
            new ArgsStatementSetter($args, $argTypes),
            new SingleColumnResultSetExtractor());
    }

    public function queryScalarBySqlStringAndArgsArray($sql, array $args, array $argTypes = null) {
        $results = $this->query(new SimpleStatementCreator($sql),
            new ArgsStatementSetter($args, $argTypes),
            new RowMapperResultSetExtractor(new SingleColumnRowMapper()));
        $count = count($results);
        if ($count != 1) {
            throw new DataAccessException('Incorrect result size, is ' . $count . ', expected 1');
        }
        return $results[0];
    }

    /**
     * Query using a prepared statement, allowing for StatementCreator and StatementSetter callbacks.
     * @param callable $psc Callback handler that can create a PreparedStatement given a PDO connection
     * @param callable $pss callback that knows how to set values on the prepared statement. If this is null, the SQL will be assumed to contain no bind parameters.
     * @param callable $rse callback that will extract results.
     * @return mixed an arbitrary result object, as returned by the $rse callback
     * @throws DataAccessException if there is any problem
     */
    public function query(callable $psc, callable $pss = null, callable $rse) {

        Assert::notNull($rse, 'ResultSetExtractor must not be null');
        $this->getLog()->debug('Executing prepared SQL query');

        return $this->execute($psc, function (PDOStatement $ps) use ($pss, $rse) {
            try {
                if ($pss != null) {
                    $pss($ps);
                }
                $ps->execute();
                $result = $rse($ps);
                $ps->closeCursor();
                return $result;
            } catch (Exception $e) {
                $ps->closeCursor();
                throw $e;
            }
        });
    }

    /**
     * @param $sql
     * @param array $args
     * @param array $argTypes
     * @return mixed
     */
    public function updateBySqlStringAndArgsArray($sql, array $args, array $argTypes = null) {
        return $this->updateBySqlString($sql, new ArgsStatementSetter($args, $argTypes));
    }

    /**
     * @param $sql
     * @param callable $pss
     * @return mixed
     */
    public function updateBySqlString($sql, callable $pss) {
        return $this->update(new SimpleStatementCreator($sql), $pss);
    }

    /**
     * @param callable $psc
     * @param callable $pss
     * @return mixed
     * @throws DataAccessException
     */
    public function update(callable $psc, callable $pss = null) {
        $this->getLog()->debug('Executing prepared SQL update');
        return $this->execute($psc, function (PDOStatement $ps) use ($pss) {
            if ($pss != null) {
                $pss($ps);
            }
            if ($ps->execute()) {
                $rows = $ps->rowCount();
                if ($this->getLog()->isDebugEnabled()) {
                    $this->getLog()->debug('SQL update affected ' . $rows . ' rows');
                }
                return $rows;
            }
            return false;
        });
    }

    /**
     * @param $sql
     * @param IBatchStatementSetter $bss
     * @return mixed
     */
    public function batchUpdateBySqlString($sql, IBatchStatementSetter $bss) {
        return $this->batchUpdate(new SimpleStatementCreator($sql), $bss);
    }

    /**
     * @param callable $psc
     * @param IBatchStatementSetter $bss
     * @return mixed
     * @throws DataAccessException
     */
    public function batchUpdate(callable $psc, IBatchStatementSetter $bss) {
        $this->getLog()->debug('Executing prepared SQL batch update');
        return $this->execute($psc, function (PDOStatement $ps) use ($bss) {
            $batchSize = $bss->getBatchSize();
            $rowsAffected = array();
            for ($i = 0; $i < $batchSize; $i++) {
                $bss->setValues($ps, $i);
                if ($ps->execute()) {
                    $rowsAffected[$i] = $ps->rowCount();
                } else {
                    throw new PDOException($ps->errorInfo(), $ps->errorCode());
                }
            }
            return $rowsAffected;
        });
    }

    //-------------------------------------------------------------------------
    // Methods dealing with prepared statements
    //-------------------------------------------------------------------------

    /**
     * @param callable $psc
     * @param callable $action
     * @return mixed
     * @throws DataAccessException
     */
    public function execute(callable $psc, callable $action) {
        Assert::notNull($psc, 'PreparedStatementCreator must not be null');
        Assert::notNull($action, 'Callback object must not be null');

        if ($this->getLog()->isDebugEnabled()) {
            $sql = self::getSql($psc);
            $this->getLog()->debug('Executing prepared SQL statement' . (!is_null($sql) ? " [" . $sql . "]" : ""));
        }

        $ps = null;
        try {
            $ps = $psc($this->pdoConnection);
            $result = $action($ps);
            return $result;
        } catch (PDOException $ex) {
            // Release Connection early, to avoid potential connection pool deadlock
            // in the case when the exception translator hasn't been initialized yet.
//            $sql = $this->getSql($psc);
            throw new DataAccessException('Bee\Persistence\Pdo\Template caught an exception', $ex);
//            throw getExceptionTranslator().translate("PreparedStatementCallback", sql, ex);
        }
    }

    /**
     * @param $sqlProvider
     * @return null|string
     */
    private static function getSql($sqlProvider) {
        if ($sqlProvider instanceof ISqlProvider) {
            return $sqlProvider->getSql();
        } else {
            return null;
        }
    }
}