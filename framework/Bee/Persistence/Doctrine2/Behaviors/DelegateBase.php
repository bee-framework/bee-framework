<?php
namespace Bee\Persistence\Doctrine2\Behaviors;
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
use Bee\Persistence\Doctrine2\EntityManagerHolder;

/**
 * User: mp
 * Date: 21.06.13
 * Time: 15:59
 */
 
class DelegateBase extends EntityManagerHolder {

	/**
	 * @var string
	 */
	private $entityName;

	/**
	 * @var array
	 */
	private $groupKeyFields;

	/**
	 * @param string $entityName
	 * @param array $groupKeyFields
	 */
	public function __construct($entityName, array $groupKeyFields) {
		\Bee_Utils_Assert::hasText($entityName, 'Entity name required, must not be empty');
		$this->entityName = $entityName;
		$this->groupKeyFields = $groupKeyFields;
	}

	/**
	 * @return string
	 */
	public function getEntityName() {
		return $this->entityName;
	}

	/**
	 * @return array
	 */
	public function getGroupKeyFields() {
		return $this->groupKeyFields;
	}
}
