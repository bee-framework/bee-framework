<?php
namespace Bee\Persistence\Pdo\Behaviors;

use Bee\Persistence\Pdo\FeatureDetector;

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

	/**
	 * @var \PDO
	 */
	private $pdo;

	/**
	 * @var string
	 */
	private $queryDomain;

	/**
	 * @param string $queryDomain
	 * @param \PDO $pdo
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
	 * @param PDOStatement $qry
	 * @param array $params
	 * @return mixed
	 */
	public static function fetchOne(PDOStatement $qry, array $params) {
		$qry->execute($params);
		$res = $qry->fetch(\PDO::FETCH_NUM);
		return $res [0];
	}

	/**
	 * @param string $feature
	 * @return bool
	 */
	protected function pdoSupportsFeature($feature) {
		return self::supports($feature, $this->getPdo());
	}
}
