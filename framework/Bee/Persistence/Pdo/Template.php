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
use Bee\Framework;
use Bee\Persistence\Exception\DataAccessException;
use Bee\Utils\Assert;

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
	 * @var Logger
	 */
	private static $log;

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

	/**
	 * @return \Logger
	 */
	public static function getLog() {
		if(!self::$log) {
			self::$log = Framework::getLoggerForClass(__CLASS__);
		}
		return self::$log;
	}

    public function __construct(PDO $pdoConnection) {
        $this->pdoConnection = $pdoConnection;
    }

    public function queryBySqlString($sql, Bee_Persistence_Pdo_IStatementSetter $pss,
                                     Bee_Persistence_Pdo_IResultSetExtractor $rse) {
        return $this->query(new Bee_Persistence_Pdo_SimpleStatementCreator($sql), $pss, $rse);
    }

    public function queryColumnBySqlStringAndArgsArray($sql, array $args, array $argTypes = null) {
        return $this->query(new Bee_Persistence_Pdo_SimpleStatementCreator($sql),
            new Bee_Persistence_Pdo_StatementSetter_Args($args, $argTypes),
            new Bee_Persistence_Pdo_ResultSetExtractor_SingleColumn());
    }

    public function queryScalarBySqlStringAndArgsArray($sql, array $args, array $argTypes = null) {
        $results = $this->query(new Bee_Persistence_Pdo_SimpleStatementCreator($sql),
            new Bee_Persistence_Pdo_StatementSetter_Args($args, $argTypes),
            new Bee_Persistence_Pdo_ResultSetExtractor_RowMapper(
            new Bee_Persistence_Pdo_RowMapper_SingleColumn()));
        $count = count($results);
        if($count != 1) {
            throw new DataAccessException('Incorrect result size, is ' .$count. ', expected 1');
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
     * @return mixed an arbitrary result object, as returned by the ResultSetExtractor
     * @throws DataAccessException if there is any problem
     */
    public function query(Bee_Persistence_Pdo_IStatementCreator $psc, Bee_Persistence_Pdo_IStatementSetter $pss,
                          Bee_Persistence_Pdo_IResultSetExtractor $rse) {

		Assert::notNull($rse, 'ResultSetExtractor must not be null');
		self::getLog()->debug('Executing prepared SQL query');

        return $this->execute($psc, new Bee_Persistence_Pdo_Template_QueryCallback($pss, $rse));
    }

    public function updateBySqlStringAndArgsArray($sql, array $args, array $argTypes = null) {
        return $this->updateBySqlString($sql, new Bee_Persistence_Pdo_StatementSetter_Args($args, $argTypes));
    }

    public function updateBySqlString($sql, Bee_Persistence_Pdo_IStatementSetter $pss) {
        return $this->update(new Bee_Persistence_Pdo_SimpleStatementCreator($sql), $pss);
    }

    public function update(Bee_Persistence_Pdo_IStatementCreator $psc, Bee_Persistence_Pdo_IStatementSetter $pss) {
		self::getLog()->debug('Executing prepared SQL update');
        return $this->execute($psc, new Bee_Persistence_Pdo_Template_UpdateCallback($pss));
    }

    public function batchUpdateBySqlString($sql, Bee_Persistence_Pdo_IBatchStatementSetter $bss) {
        return $this->batchUpdate(new Bee_Persistence_Pdo_SimpleStatementCreator($sql), $bss);
    }

    public function batchUpdate(Bee_Persistence_Pdo_IStatementCreator $psc, Bee_Persistence_Pdo_IBatchStatementSetter $bss) {
		self::getLog()->debug('Executing prepared SQL batch update');
        return $this->execute($psc, new Bee_Persistence_Pdo_Template_BatchUpdateCallback($bss));
    }

    //-------------------------------------------------------------------------
    // Methods dealing with prepared statements
    //-------------------------------------------------------------------------

    public function execute(Bee_Persistence_Pdo_IStatementCreator $psc, Bee_Persistence_Pdo_IStatementCallback $action) {

        Assert::notNull($psc, 'PreparedStatementCreator must not be null');
        Assert::notNull($action, 'Callback object must not be null');

        if (self::getLog()->isDebugEnabled()) {
            $sql = self::getSql($psc);
			self::getLog()->debug('Executing prepared SQL statement' . (!is_null($sql) ? " [" . $sql . "]" : ""));
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
            throw new DataAccessException('Bee_Persistence_Pdo_Template caught an exception', $ex);
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
			$result = $this->rse->extractData($ps);
			$ps->closeCursor();
			return $result;
        } catch(Exception $e) {
            $ps->closeCursor();
            throw $e;
        }
    }
}

class Bee_Persistence_Pdo_Template_UpdateCallback implements Bee_Persistence_Pdo_IStatementCallback {

    /**
     * @var Bee_Persistence_Pdo_IStatementSetter
     */
    private $pss;

	/**
	 * @var Logger
	 */
	private static $log;

    public function __construct(Bee_Persistence_Pdo_IStatementSetter $pss) {
        $this->pss = $pss;
    }

	public static function getLog() {
		if(!self::$log) {
			self::$log = Framework::getLoggerForClass(__CLASS__);
		}
		return self::$log;
	}

    function doInPreparedStatement(PDOStatement $ps) {
        if ($this->pss != null) {
            $this->pss->setValues($ps);
        }
        if($ps->execute()) {
            $rows = $ps->rowCount();
            if(self::getLog()->isDebugEnabled()) {
				self::getLog()->debug('SQL update affected '.$rows.' rows');
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
