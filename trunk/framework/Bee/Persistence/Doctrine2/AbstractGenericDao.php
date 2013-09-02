<?php
namespace Bee\Persistence\Doctrine2;
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
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use UnexpectedValueException;

/**
 * User: mp
 * Date: 02.09.13
 * Time: 20:35
 */
 
abstract class AbstractGenericDao extends DaoBase {

	/**
	 * @param mixed $id
	 * @throws UnexpectedValueException
	 * @return mixed
	 */
	public function getById($id) {
		$qb = $this->getBaseQuery();
		$this->applyWhereId($id, $qb);
		return $this->getSingleResult($qb);
	}

	/**
	 * @param QueryBuilder $qb
	 * @return mixed
	 */
	protected function getSingleResult(QueryBuilder $qb) {
		$q = $qb->getQuery();
		$this->decorateQuery($q);
		return $q->getSingleResult($this->getHydrationMode());
	}

	/**
	 * @param Query $q
	 */
	protected function decorateQuery(Query $q) {
	}

	/**
	 * @return QueryBuilder
	 */
	protected function getBaseQuery() {
		$baseEntityAlias = $this->getEntityAlias();
		$indexBy = count($this->getIdFieldName()) > 1 ? null : $baseEntityAlias . '.' . $this->getIdFieldName();
		return $this->getEntityManager()->createQueryBuilder()->select($baseEntityAlias)
				->from($this->getEntity(), $baseEntityAlias, $indexBy);
	}

	/**
	 * @return string
	 */
	protected function getEntityAlias() {
		return 'e';
	}

	/**
	 * @return null
	 */
	protected function getHydrationMode() {
		return null;
	}

	/**
	 * @return string
	 */
	public abstract function getEntity();

	/**
	 * @return string|array
	 */
	protected function getIdFieldName() {
		$classMetadata = $this->getEntityManager()->getClassMetadata($this->getEntity());
		$idFields = $classMetadata->getIdentifierFieldNames();
		return count($idFields) > 1 ? $idFields : $idFields[0];
	}

	/**
	 * @param mixed $id
	 * @param QueryBuilder $qb
	 * @throws \UnexpectedValueException
	 */
	protected function applyWhereId($id, QueryBuilder $qb) {
		$idFields = $this->getIdFieldName();

		$expectedDim = count($idFields);
		$actualDim = count($id);

		// unpack single-valued id if necessary
		if (is_array($id) && $actualDim === 1) {
			$id = $id[0];
		}

		$baseEntityAlias = $this->getEntityAlias();
		if ($expectedDim > 1) {
			// composite key
			if ($actualDim === 1) {
				$id = DaoUtils::explodeScalarId($id, $idFields);
			} else if ($actualDim !== $expectedDim) {
				throw new UnexpectedValueException('Dimension of given ID (' . count($id) . ') does not match expected dimension (' . count($idFields) . ').');
			}

			// here we can be sure that the dimensions match - both branches above would have thrown otherwise
			$whereParts = array();
			array_walk($id, function ($value, $key) use ($baseEntityAlias, &$whereParts) {
				$whereParts[] = $baseEntityAlias . '.' . $key . ' = ' . ':' . $key;
			});
			$qb->where(implode(' AND ', $whereParts))->setParameters($id);
		} else {
			$qb->where($baseEntityAlias . '.' . $idFields . ' = :id')->setParameter('id', $id);
		}
	}
}
