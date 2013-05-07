<?php
namespace Bee\Persistence\Pdo\Behaviors;

use Bee\Persistence\Behaviors\Ordered\IDelegate;

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
 * Date: 05.05.13
 * Time: 23:23
 */
class GenericOrderedDelegate extends DelegateBase implements IDelegate {

	const GET_POS_QUERY_TEMPLATE = 'SELECT %1$s FROM %2$s WHERE %3$s AND %4$s';
	const MAX_POS_QUERY_TEMPLATE = 'SELECT MAX(%1$s) FROM %2$s WHERE %3$s';
	const GROUP_QUERY_TEMPLATE = 'SELECT %1$s FROM %2$s WHERE %3$s';
	const SHIFT_UP_QUERY_TEMPLATE = 'UPDATE %2$s SET %1$s = %1$s + 1 WHERE %1$s >= :newPos AND %1$s < :oldPos AND %3$s';
	const SHIFT_DOWN_QUERY_TEMPLATE = 'UPDATE %2$s SET %1$s = %1$s - 1 WHERE %1$s <= :newPos AND %1$s > :oldPos AND %3$s';
	const SET_POS_QUERY_TEMPLATE = 'UPDATE %2$s SET %1$s = :newPos WHERE %3$s AND %4$s';

	/**
	 * @var string
	 */
	private $posExpression = 'pos';

	/**
	 * @var string
	 */
	private $idFieldName = 'id';

	/**
	 * @var string
	 */
	private $groupFieldName;

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
	 * @param string $idFieldName
	 */
	public function setIdFieldName($idFieldName) {
		$this->idFieldName = $idFieldName;
	}

	/**
	 * @return string
	 */
	public function getIdFieldName() {
		return $this->idFieldName;
	}

	/**
	 * @param string $domainFieldName
	 */
	public function setGroupFieldName($domainFieldName) {
		$this->groupFieldName = $domainFieldName;
	}

	/**
	 * @return string
	 */
	public function getGroupFieldName() {
		return $this->groupFieldName;
	}

	/**
	 * @param $orderedEntity
	 * @param array $params
	 * @param bool $restriction
	 * @return string
	 */
	protected function getDomainRestrictionString($orderedEntity, array &$params, $restriction = false) {
		$result = '1=1';
		if ($this->getGroupFieldName()) {
			if ($restriction === false) {
				// determine group value
				$grpParams = array();
				$qryString = sprintf(self::GROUP_QUERY_TEMPLATE, $this->getGroupFieldName(), $this->getQueryDomain(), $this->getIdentityRestrictionString($orderedEntity, $params));
				$restriction = self::fetchOne($this->getPdo()->prepare($qryString), $grpParams);
			}
			$result = $this->doCreateRestrictionString($params, $restriction);
		}
		return $result;
	}

	/**
	 * @param array $params
	 * @param $restriction
	 * @return string
	 */
	protected function doCreateRestrictionString(array &$params, $restriction) {
		$params[':group_id'] = $restriction;
		return $this->getGroupFieldName() . ' = :group_id';
	}

	/**
	 * @param $orderedEntity object representing the entity to be manipulated. Default implementation assumes that this
	 * is the actual id as stored in the database
	 * @param array $params
	 * @return string
	 */
	protected function getIdentityRestrictionString($orderedEntity, array &$params) {
		$params[':id'] = $orderedEntity;
		return $this->getIdFieldName() . ' = :id';
	}

	/**
	 * @param $orderedEntity
	 * @param $restriction
	 * @return mixed
	 */
	public function getPosition($orderedEntity, $restriction = false) {
		$params = array();
		$qryString = sprintf(self::GET_POS_QUERY_TEMPLATE, $this->getPosExpression(), $this->getQueryDomain(),
			$this->getIdentityRestrictionString($orderedEntity, $params), $this->getDomainRestrictionString($orderedEntity, $params, $restriction));
		return self::fetchOne($this->getPdo()->prepare($qryString), $params);
	}

	/**
	 * @param $orderedEntity
	 * @param $restriction
	 * @return mixed
	 */
	public function getMaxPosition($orderedEntity, $restriction = false) {
		$params = array();
		$qryString = sprintf(self::MAX_POS_QUERY_TEMPLATE, $this->getPosExpression(), $this->getQueryDomain(),
			$this->getDomainRestrictionString($orderedEntity, $params, $restriction));
		return self::fetchOne($this->getPdo()->prepare($qryString), $params);
	}

	/**
	 * @param $orderedEntity
	 * @param int $newPos
	 * @param int $oldPos
	 * @param mixed $restriction
	 */
	public function shiftPosition($orderedEntity, $newPos, $oldPos, $restriction = false) {
		$params = array(':newPos' => $newPos, ':oldPos' => $oldPos);
		$qryDomain = $this->getQueryDomain();
		$qryString = sprintf($newPos < $oldPos ? self::SHIFT_UP_QUERY_TEMPLATE : self::SHIFT_DOWN_QUERY_TEMPLATE,
			$this->getPosExpression(), $qryDomain, $this->getDomainRestrictionString($orderedEntity, $params, $restriction));
		// if this is a single table update, add ORDER clause to avoid unique constraint violation
		if (stripos($qryDomain, ' JOIN ') === false) {
			$qryString .= ' ORDER BY ' . $this->getPosExpression() . ($newPos < $oldPos ? ' DESC' : ' ASC');
		}
		$this->getPdo()->prepare($qryString)->execute($params);
	}

	/**
	 * @param mixed $orderedEntity
	 * @param int $newPos
	 * @param mixed $restriction
	 */
	public function setPosition($orderedEntity, $newPos, $restriction) {
		$params = array(':newPos' => $newPos);
		$qryString = sprintf(self::SET_POS_QUERY_TEMPLATE, $this->getPosExpression(), $this->getQueryDomain(),
			$this->getIdentityRestrictionString($orderedEntity, $params), $this->getDomainRestrictionString($orderedEntity, $params, $restriction));
		$this->getPdo()->prepare($qryString)->execute($params);
	}
}
