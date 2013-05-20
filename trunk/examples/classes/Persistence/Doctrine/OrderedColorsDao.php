<?php
namespace Persistence\Doctrine;
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
use Bee\Persistence\Doctrine\Behaviors\GenericOrderedDelegate;
use Bee\Persistence\Behaviors\Ordered\Strategy as OrderedStrategy;

/**
 * User: mp
 * Date: 20.05.13
 * Time: 14:11
 */
 
class OrderedColorsDao extends \Bee_Persistence_Doctrine_DaoBase {

	const ENTITY_CLASS_NAME = 'Persistence\Doctrine\OrderedColorsEntity';

	/**
	 * @var OrderedStrategy
	 */
	private $orderedStrategy;

	public function __construct(\Doctrine_Connection $conn) {
		$this->setDoctrineConnection($conn);
		$pdoOrderedDelagate = new GenericOrderedDelegate(self::ENTITY_CLASS_NAME, $conn);
		$this->orderedStrategy = new OrderedStrategy($pdoOrderedDelagate);
	}

	/**
	 * @return \Bee\Persistence\Behaviors\Ordered\Strategy
	 */
	public function getOrderedStrategy() {
		return $this->orderedStrategy;
	}

	public function createTable() {
		\Doctrine_Core::createTablesFromArray(array(self::ENTITY_CLASS_NAME));
	}

	public function deleteAllColors() {
		$this->doInTransaction(function(OrderedColorsDao $dao, \Logger $log) {
			$log->debug('deleting all colors');
			$dao->getDoctrineConnection()->createQuery()->delete(OrderedColorsDao::ENTITY_CLASS_NAME)->execute();
		});
	}

	public function addColor($colorName, $colorHex) {
		self::getLog()->info("adding color ($colorName, $colorHex)");
		return $this->doInTransaction(function(OrderedColorsDao $dao, \Logger $log) use ($colorName, $colorHex) {
			$log->debug('inserting');
			$entity = new OrderedColorsEntity();
			$entity->setName($colorName);
			$entity->setHexValue($colorHex);
			$entity->save($dao->getDoctrineConnection());

			$log->debug('moving to end of list');
			$pos = $dao->getOrderedStrategy()->moveToEnd($entity);

			$log->debug("committing ($entity->id, $colorName, $colorHex, $pos)");
			return $entity;
		});
	}
}
