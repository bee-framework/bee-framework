<?php
namespace Bee\Persistence\Pdo\Behaviors;

use Bee\Persistence\Behaviors\AbstractFieldBasedDelegate;
use Bee\Persistence\Pdo\FeatureDetector;
use Bee\Persistence\Pdo\Utils;

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

use \PDOStatement;

/**
 * User: mp
 * Date: 05.05.13
 * Time: 23:52
 */
abstract class DelegateBase extends AbstractFieldBasedDelegate {

	const GROUP_QUERY_TEMPLATE = 'SELECT %1$s FROM %2$s WHERE %3$s';

	/**
	 * @var \PDO
	 */
	private $pdo;

	/**
	 * @param string $queryDomain
	 * @param \PDO $pdo
	 * @return \Bee\Persistence\Pdo\Behaviors\DelegateBase
	 */
	public function __construct($queryDomain, \PDO $pdo) {
		parent::__construct($queryDomain);
		$this->pdo = $pdo;
	}

	/**
	 * @return \PDO
	 */
	public function getPdo() {
		return $this->pdo;
	}

	/**
	 * @param string $feature
	 * @return bool
	 */
	protected function pdoSupportsFeature($feature) {
		return FeatureDetector::supports($feature, $this->getPdo());
	}

	/**
	 * @param $entity object representing the entity to be manipulated. Default implementation assumes that this
	 * is the actual id as stored in the database
	 * @param array $params
	 * @return string
	 */
	protected function getIdentityRestrictionString($entity, array &$params) {
		$params[':id'] = $entity;
		return $this->getIdFieldName() . ' = :id';
	}

	/**
	 * @param $entity
	 * @param array $params
	 * @param bool $restriction
	 * @return string
	 */
	protected function getDomainRestrictionString($entity, array &$params, $restriction = false) {
		$result = '1=1';
		if ($this->getGroupFieldName()) {
			if ($restriction === false) {
				// determine group value
				$restriction = $this->getGroup($entity);
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
	 * @param $entity
	 * @return mixed
	 */
	protected function getGroup($entity) {
		// determine group value
		$params = array();
		$qryString = sprintf(self::GROUP_QUERY_TEMPLATE, $this->getGroupFieldName(), $this->getQueryDomain(), $this->getIdentityRestrictionString($entity, $params));
		return Utils::fetchOne($this->getPdo()->prepare($qryString), $params);
	}
}
