<?php
namespace Persistence\Pdo;

use Bee\Persistence\Behaviors\Ordered\Strategy as OrderedStrategy;
use Bee\Persistence\Pdo\Behaviors\GenericOrderedDelegate;

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
 * Date: 07.05.13
 * Time: 12:12
 */
 
class OrderedColorsDao {

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

	/**
	 * @var OrderedStrategy
	 */
	private $orderedStrategy;

	public function __construct(\PDO $pdoConnection) {
		self::getLog()->info('DAO constructed, got PDO connection');
		$this->pdoConnection = $pdoConnection;
		$pdoOrderedDelagate = new GenericOrderedDelegate('ordered_colors', $pdoConnection);
		$this->orderedStrategy = new OrderedStrategy($pdoOrderedDelagate);
	}

	/**
	 *
	 */
	public function createTable() {
		$this->pdoConnection->exec('CREATE TABLE "ordered_colors" (
			 "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
			 "name" text NOT NULL,
			 "hex_value" text NOT NULL,
			 "pos" integer DEFAULT NULL,
			UNIQUE (pos ASC)
		)');
	}

	/**
	 * @return \Bee\Persistence\Behaviors\Ordered\Strategy
	 */
	public function getOrderedStrategy() {
		return $this->orderedStrategy;
	}

	public function addColor($colorName, $colorHex) {
		self::getLog()->info("adding color ($colorName, $colorHex)");

		$this->pdoConnection->beginTransaction();
		try {

			self::getLog()->debug('inserting');
			$insertStmt = $this->pdoConnection->prepare('INSERT INTO ordered_colors (name, hex_value) VALUES (:name, :hex_value)');
			$insertStmt->execute(array(':name' => $colorName, ':hex_value' => $colorHex));

			$id = $this->pdoConnection->lastInsertId();
			self::getLog()->debug('moving to end of list');
			$pos = $this->orderedStrategy->moveToEnd($id);

			self::getLog()->debug("committing ($id, $colorName, $colorHex, $pos)");
			$this->pdoConnection->commit();
			return $id;
		} catch(\Exception $e) {
			self::getLog()->debug('exception caught', $e);
			$this->pdoConnection->rollBack();
			throw $e;
		}
	}
}
