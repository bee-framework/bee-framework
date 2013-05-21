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
		$delagate = new GenericNestedSetDelegate('simple_hierarchy_pdo', $pdoConnection);
		$delagate->setGroupFieldName('root_id');
		$this->nestedSetStrategy = new NestedSetStrategy($delagate);
	}

	/**
	 *
	 */
	public function createTable() {
		$drvName = $this->getPdoConnection()->getAttribute(\PDO::ATTR_DRIVER_NAME);
		switch ($drvName) {
			case 'mysql':
				$this->getPdoConnection()->exec('CREATE TABLE IF NOT EXISTS `simple_hierarchy_pdo` (
					id bigint(20) NOT NULL AUTO_INCREMENT,
					name varchar(255) NOT NULL,
					root_id int(11) NOT NULL,
					lft int(11) DEFAULT NULL,
					rgt int(11) DEFAULT NULL,
					lvl int(11) DEFAULT NULL,
					PRIMARY KEY (id),
					UNIQUE KEY grp_lft (root_id, lft),
					UNIQUE KEY grp_rgt (root_id, rgt))
					ENGINE=InnoDB CHARSET=utf8'
				);
				break;
			case 'sqlite':
				$this->getPdoConnection()->exec('CREATE TABLE IF NOT EXISTS "simple_hierarchy_pdo" (
					"id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
					"name" text NOT NULL,
					"root_id" integer NOT NULL,
					"lft" integer DEFAULT NULL,
					"rgt" integer DEFAULT NULL,
					"lvl" integer DEFAULT NULL)'
				);
				break;
		}
	}

	public function deleteAll() {
		$this->doInTransaction(function(SimpleHierarchyDao $dao, \Logger $log) {
			$log->debug('deleting all hierarchy entries');
			$dao->getPdoConnection()->exec('DELETE FROM simple_hierarchy_pdo');
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
			$insertStmt = $dao->getPdoConnection()->prepare('INSERT INTO simple_hierarchy_pdo (name) VALUES (:name)');
			$insertStmt->execute(array(':name' => $name));

			$id = $dao->getPdoConnection()->lastInsertId();
			$log->debug('moving to end of root list');
			$info = $dao->getNestedSetStrategy()->moveAsLastRoot($id);

			$log->debug("committing ($id, $name, $info)");
			return $id;
		});
	}
}
