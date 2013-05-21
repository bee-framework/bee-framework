<?php
namespace Persistence\Pdo;

use Bee\Persistence\Behaviors\Ordered\Strategy as OrderedStrategy;
use Bee\Persistence\Pdo\Behaviors\GenericOrderedDelegate;
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
 * Time: 12:12
 */

class OrderedColorsDao extends SimpleDaoBase {

	/**
	 * @var OrderedStrategy
	 */
	private $orderedStrategy;

	public function __construct(\PDO $pdoConnection) {
		parent::__construct($pdoConnection);
		$delagate = new GenericOrderedDelegate('ordered_colors_pdo', $pdoConnection);
		$delagate->setGroupFieldName('color_grp');
		$this->orderedStrategy = new OrderedStrategy($delagate);
	}

	/**
	 *
	 */
	public function createTable() {
		$drvName = $this->getPdoConnection()->getAttribute(\PDO::ATTR_DRIVER_NAME);
		switch ($drvName) {
			case 'mysql':
				$this->getPdoConnection()->exec('CREATE TABLE IF NOT EXISTS `ordered_colors_pdo` (
					id bigint(20) NOT NULL AUTO_INCREMENT,
					name varchar(255) NOT NULL,
					hex_value varchar(7) NOT NULL,
					color_grp int(11) NOT NULL,
					pos int(11) DEFAULT NULL,
					PRIMARY KEY (id),
					UNIQUE KEY grp_pos_idx (color_grp, pos))
					ENGINE=InnoDB CHARSET=utf8'
				);
				break;
			case 'sqlite':
				$this->getPdoConnection()->exec('CREATE TABLE IF NOT EXISTS "ordered_colors_pdo" (
					"id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
					"name" text NOT NULL,
					"hex_value" text NOT NULL,
					"color_grp" integer NOT NULL,
					"pos" integer DEFAULT NULL)'
				);
				break;
		}
	}

	/**
	 * @return \Bee\Persistence\Behaviors\Ordered\Strategy
	 */
	public function getOrderedStrategy() {
		return $this->orderedStrategy;
	}

	public function deleteAllColors() {
		$this->doInTransaction(function (OrderedColorsDao $dao, \Logger $log) {
			$log->debug('deleting all colors');
			$dao->getPdoConnection()->exec('DELETE FROM ordered_colors_pdo');
		});
	}

	public function addColor($colorName, $colorHex, $grpId) {
		self::getLog()->info("adding color ($colorName, $colorHex, $grpId)");
		return $this->doInTransaction(function (OrderedColorsDao $dao, \Logger $log) use ($colorName, $colorHex, $grpId) {
			$log->debug('inserting');
			$insertStmt = $dao->getPdoConnection()->prepare('INSERT INTO ordered_colors_pdo (name, hex_value, color_grp) VALUES (:name, :hex_value, :grp_id)');
			$insertStmt->execute(array(':name' => $colorName, ':hex_value' => $colorHex, ':grp_id' => $grpId));

			$id = $dao->getPdoConnection()->lastInsertId();
			$log->debug('moving to end of list');
			$pos = $dao->getOrderedStrategy()->moveToEnd($id);

			$log->debug("committing ($id, $colorName, $colorHex, $grpId, $pos)");
			return $id;

		});
	}
}
