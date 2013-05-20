<?php
namespace Persistence\Pdo;

use Bee\Persistence\Behaviors\NestedSet\Strategy as NestedSetStrategy;
use Bee\Persistence\Pdo\Behaviors\GenericNestedSetDelegate;
use Bee\Persistence\Pdo\SimpleDaoBase;

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
 * Time: 23:46
 */
class SimpleHierarchyDao extends SimpleDaoBase {

	/**
	 * @var NestedSetStrategy
	 */
	private $nestedSetStrategy;

	public function __construct(\PDO $pdoConnection) {
		parent::__construct($pdoConnection);
		$pdoNestedSetDelagate = new GenericNestedSetDelegate('simple_hierarchy', $pdoConnection);
		$this->nestedSetStrategy = new NestedSetStrategy($pdoNestedSetDelagate);
	}

	/**
	 *
	 */
	public function createTable() {
		$this->getPdoConnection()->exec('CREATE TABLE IF NOT EXISTS "simple_hierarchy" (
			 "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
			 "name" text NOT NULL,
			 "lft" integer DEFAULT NULL,
			 "rgt" integer DEFAULT NULL,
			 "lvl" integer DEFAULT NULL)'
		);
	}

	public function deleteAll() {
		$this->doInTransaction(function(SimpleHierarchyDao $dao, \Logger $log) {
			$log->debug('deleting all hierarchy entries');
			$dao->getPdoConnection()->exec('DELETE FROM simple_hierarchy');
		});
	}

	/**
	 * @return \Bee\Persistence\Behaviors\NestedSet\Strategy
	 */
	public function getNestedSetStrategy() {
		return $this->nestedSetStrategy;
	}

	public function addEntry($name) {
		return $this->doInTransaction(function(SimpleHierarchyDao $dao, \Logger $log) use ($name) {
			$log->info("adding entry ($name)");
			$log->debug('inserting');
			$insertStmt = $dao->getPdoConnection()->prepare('INSERT INTO simple_hierarchy (name) VALUES (:name)');
			$insertStmt->execute(array(':name' => $name));

			$id = $dao->getPdoConnection()->lastInsertId();
			$log->debug('moving to end of root list');
			$info = $dao->getNestedSetStrategy()->moveAsLastRoot($id);

			$log->debug("committing ($id, $name, $info)");
			return $id;
		});
	}
}
