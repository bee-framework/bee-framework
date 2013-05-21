<?php
namespace Bee\Persistence\Behaviors;
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
use Bee\Persistence\Pdo\FeatureDetector;

/**
 * User: mp
 * Date: 21.05.13
 * Time: 10:19
 */
 
abstract class AbstractFieldBasedDelegate {

	/**
	 * @var string
	 */
	private $idFieldName = 'id';

	/**
	 * @var string
	 */
	private $groupFieldName;

	/**
	 * @var string
	 */
	private $queryDomain;

	/**
	 * @param string $queryDomain
	 */
	public function __construct($queryDomain) {
		\Bee_Utils_Assert::hasText($queryDomain, 'Query domain (table / entity name) required, must not be empty');
		$this->queryDomain = $queryDomain;
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
	 * @return string
	 */
	public function getQueryDomain() {
		return $this->queryDomain;
	}
}
