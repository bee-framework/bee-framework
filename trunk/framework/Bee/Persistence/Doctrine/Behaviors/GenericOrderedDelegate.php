<?php
namespace Bee\Persistence\Doctrine\Behaviors;
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
use Bee\Persistence\Behaviors\Ordered\IDelegate;
use Bee\Persistence\Pdo\FeatureDetector;
use Bee\Persistence\Pdo\Utils;

/**
 * User: mp
 * Date: 20.05.13
 * Time: 02:17
 */

class GenericOrderedDelegate extends DelegateBase implements IDelegate {

	/**
	 * @var string
	 */
	private $posExpression = 'pos';

	/**
	 * @param string $posFieldName
	 */
	public function setPosExpression($posFieldName) {
		$this->posExpression = $posFieldName;
	}

	/**
	 * @return string
	 */
	public function getPosExpression() {
		return $this->posExpression;
	}

	/**
	 * @param mixed $orderedEntity
	 * @param mixed $restriction
	 * @return int|bool
	 */
	public function getPosition($orderedEntity, $restriction = false) {
		$qry = $this->getIdentityBaseQuery($orderedEntity, $restriction);
		return Utils::numericOrFalse($this->addPositionSelect($qry)->fetchOne(array(), \Doctrine_Core::HYDRATE_SINGLE_SCALAR));
	}

	/**
	 * @param mixed $orderedEntity
	 * @param mixed $restriction
	 * @return int
	 */
	public function getMaxPosition($orderedEntity, $restriction = false) {
		$qry = $this->addGroupRestriction($this->getEntityBaseQuery($orderedEntity, $restriction), $orderedEntity, $restriction);
		$result = $this->addMaxPositionSelect($qry)->fetchOne(array(), \Doctrine_Core::HYDRATE_SINGLE_SCALAR);
		return Utils::numericOrFalse($result);
	}

	/**
	 * @param mixed $orderedEntity
	 * @param int $newPos
	 * @param int|bool $oldPos
	 * @param bool $restriction
	 * @return void
	 */
	public function shiftPosition($orderedEntity, $newPos, $oldPos, $restriction = false) {
		$params = array();

		$qry = $this->addGroupRestriction($this->getEntityUpdateBaseQuery(), $orderedEntity, $restriction);

		if($oldPos !== false) {
			if($newPos !== false) {
				$params[':newPos'] = $newPos;
				$params[':oldPos'] = $oldPos;
				if($newPos < $oldPos) {
					// shift up: + 1 WHERE %1$s >= :newPos AND %1$s < :oldPos
					$qry->set($this->getPosExpression(), $this->getPosExpression() . ' + 1');
					$qry->addWhere($this->getPosExpression() . ' >= :newPos');
					$qry->addWhere($this->getPosExpression() . ' < :oldPos');
				} else {
					// shift down: - 1 WHERE %1$s <= :newPos AND %1$s > :oldPos
					$qry->set($this->getPosExpression(), $this->getPosExpression() . ' - 1');
					$qry->addWhere($this->getPosExpression() . ' <= :newPos');
					$qry->addWhere($this->getPosExpression() . ' > :oldPos');
				}
			} else {
				// shift down:  - 1 WHERE %1$s > :oldPos
				$qry->set($this->getPosExpression(), $this->getPosExpression() . ' - 1');
				$qry->addWhere($this->getPosExpression() . ' > :oldPos');
				$params[':oldPos'] = $oldPos;
			}
		} else {
			// shift up: + 1 WHERE %1$s >= :newPos
			$qry->set($this->getPosExpression(), $this->getPosExpression() . ' + 1');
			$qry->addWhere($this->getPosExpression() . ' >= :newPos');
			$params[':newPos'] = $newPos;
		}


		// if this is a single table update, add ORDER clause to avoid unique constraint violation (if driver supports it)
		if ($oldPos !== false && $this->pdoSupportsFeature(FeatureDetector::FEATURE_ORDERED_UPDATE) /*&& stripos($qryDomain, ' JOIN ') === false*/) {
			$qry->orderBy($this->getPosExpression() . ($newPos !== false && ($newPos < $oldPos) ? ' DESC' : ' ASC'));
		}

		$qry->execute($params);
	}

	/**
	 * @param mixed $orderedEntity
	 * @param int $newPos
	 * @param mixed $restriction
	 */
	public function setPosition($orderedEntity, $newPos, $restriction = false) {
		$qry = $this->addGroupRestriction($this->getEntityUpdateBaseQuery(), $orderedEntity, $restriction);
		$this->addIdentityRestriction($qry, $orderedEntity);
		$qry->set($this->getPosExpression(), is_null($newPos) ? 'NULL' : $newPos)->execute();
	}

	/**
	 * @param mixed $orderedEntity
	 * @param mixed $restriction
	 * @return \Doctrine_Query
	 */
	protected function getIdentityBaseQuery($orderedEntity, $restriction = false) {
		$qry = $this->getEntityBaseQuery();
		$this->addIdentityRestriction($qry, $orderedEntity);
		return $this->addGroupRestriction($qry, $orderedEntity, $restriction);
	}

	/**
	 * @param \Doctrine_Query $qry
	 * @return \Doctrine_Query
	 */
	protected function addPositionSelect(\Doctrine_Query $qry) {
		return $qry->select($this->getPosExpression());
	}

	/**
	 * @param \Doctrine_Query $qry
	 * @return \Doctrine_Query
	 */
	protected function addMaxPositionSelect(\Doctrine_Query $qry) {
		return $qry->select('MAX('.$this->getPosExpression().')');
	}
}
