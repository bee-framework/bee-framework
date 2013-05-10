<?php
namespace Bee\Persistence\Pdo;

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
 * Date: 08.05.13
 * Time: 12:44
 */
 
class SimpleDaoBase {

	/**
	 * @var \Logger
	 */
	private static $log;

	/**
	 * @return \Logger
	 */
	protected static function getLog() {
		if (!self::$log) {
			self::$log = \Bee_Framework::getLoggerForClass(__CLASS__);
		}
		return self::$log;
	}

	/**
	 * @var \PDO
	 */
	private $pdoConnection;

	public function __construct(\PDO $pdoConnection) {
		self::getLog()->info('DAO constructed, got PDO connection');
		$this->pdoConnection = $pdoConnection;
	}

	/**
	 * @return \PDO
	 */
	public function getPdoConnection() {
		return $this->pdoConnection;
	}

	/**
	 * @param callback $func
	 * @throws \Exception
	 * @return mixed
	 */
	public function doInTransaction($func) {
		$this->pdoConnection->beginTransaction();
		try {
			$result = $func($this, self::getLog());

			$this->pdoConnection->commit();

			return $result;
		} catch(\Exception $e) {
			self::getLog()->debug('exception caught', $e);
			$this->pdoConnection->rollBack();
			throw $e;
		}

	}
}
