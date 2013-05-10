<?php
namespace Bee\Persistence\Pdo\Behaviors;

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
abstract class DelegateBase extends FeatureDetector {

	const GROUP_QUERY_TEMPLATE = 'SELECT %1$s FROM %2$s WHERE %3$s';

	/**
	 * @var \PDO
	 */
	private $pdo;

	/**
	 * @var string
	 */
	private $queryDomain;

	/**
	 * @var string
	 */
	private $idFieldName = 'id';

	/**
	 * @var string
	 */
	private $groupFieldName;

	/**
	 * @param string $queryDomain
	 * @param \PDO $pdo
	 * @return \Bee\Persistence\Pdo\Behaviors\DelegateBase
	 */
	public function __construct($queryDomain, \PDO $pdo) {
		\Bee_Utils_Assert::hasText($queryDomain, 'Query domain (table name / table joins) required, must not be empty');
		$this->queryDomain = $queryDomain;
		$this->pdo = $pdo;
	}

	/**
	 * @return \PDO
	 */
	public function getPdo() {
		return $this->pdo;
	}

	/**
	 * @return string
	 */
	public function getQueryDomain() {
		return $this->queryDomain;
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
	 * @param string $feature
	 * @return bool
	 */
	protected function pdoSupportsFeature($feature) {
		return self::supports($feature, $this->getPdo());
	}

	/**
	 * @param $entity object representing the entity to be manipulated. Default implementation assumes that this
	 * is the actual id as stored in the database
	 * @param array $params
	 * @return string
	 */
	protected function getIdentityRestrictionString($entity, array &$params) {
		$params[':id'] = $entity;
		return $this->idFieldName . ' = :id';
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
				$grpParams = array();
				$qryString = sprintf(self::GROUP_QUERY_TEMPLATE, $this->getGroupFieldName(), $this->getQueryDomain(), $this->getIdentityRestrictionString($entity, $params));
				$restriction = Utils::fetchOne($this->getPdo()->prepare($qryString), $grpParams);
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
}
