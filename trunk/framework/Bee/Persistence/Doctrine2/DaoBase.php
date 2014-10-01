<?php
namespace Bee\Persistence\Doctrine2;

/*
 * Copyright 2008-2014 the original author or authors.
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
use Bee\Persistence\IOrderAndLimitHolder;
use Bee\Persistence\IRestrictionHolder;
use Bee_Utils_Strings;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;

/**
 * User: mp
 * Date: 05.05.13
 * Time: 17:26
 */
class DaoBase extends EntityManagerHolder {

	/**
	 * @param QueryBuilder $queryBuilder
	 * @param IRestrictionHolder $restrictionHolder
	 * @param IOrderAndLimitHolder $orderAndLimitHolder
	 * @param array $defaultOrderMapping
	 * @param null $hydrationMode
	 * @return array
	 */
	public function executeListQuery(QueryBuilder $queryBuilder, IRestrictionHolder $restrictionHolder = null, IOrderAndLimitHolder $orderAndLimitHolder = null, array $defaultOrderMapping = null, $hydrationMode = null) {
		$this->applyFilterRestrictions($queryBuilder, $restrictionHolder);
		$this->applyOrderMapping($queryBuilder, $orderAndLimitHolder, $defaultOrderMapping);
		$q = $this->getQueryFromBuilder($queryBuilder);
		if(!is_null($hydrationMode)) {
			$q->setHydrationMode($hydrationMode);
		}
		return $this->getPaginatedOrderedResultFromQuery($q, $orderAndLimitHolder);
	}

	/**
	 * @param QueryBuilder $qb
	 * @return Query
	 */
	protected function getQueryFromBuilder(QueryBuilder $qb) {
		return $qb->getQuery();
	}

	/**
	 * @param QueryBuilder $queryBuilder
	 * @param IRestrictionHolder $restrictionHolder
	 * @internal param QueryBuilder $query
	 */
	protected final function applyFilterRestrictions(QueryBuilder &$queryBuilder, IRestrictionHolder $restrictionHolder = null) {
		if (is_null($restrictionHolder)) {
			return;
		}

		if (!Bee_Utils_Strings::hasText($restrictionHolder->getFilterString())) {
			return;
		}

		$filterTokens = Bee_Utils_Strings::tokenizeToArray($restrictionHolder->getFilterString(), ' ');
		foreach ($filterTokens as $no => $token) {
			$andWhereString = '';
			$params = array();

			$tokenName = 'filtertoken' . $no;
			$params[$tokenName] = '%' . $token . '%';

			foreach ($restrictionHolder->getFilterableFields() as $fieldName) {
				// $fieldName MUST BE A DOCTRINE NAME
				if (Bee_Utils_Strings::hasText($andWhereString)) {
					$andWhereString .= ' OR ';
				}

				$andWhereString .= $fieldName . ' LIKE :' . $tokenName;
			}

			if (Bee_Utils_Strings::hasText($andWhereString)) {
				$queryBuilder->andWhere($andWhereString);

				foreach ($params as $key => $value) {
					$queryBuilder->setParameter($key, $value);
				}
			}
		}
	}

	/**
	 * @param QueryBuilder $queryBuilder
	 * @param IOrderAndLimitHolder $orderAndLimitHolder
	 * @param array $defaultOrderMapping
	 */
	protected final function applyOrderMapping(QueryBuilder &$queryBuilder, IOrderAndLimitHolder $orderAndLimitHolder = null, array $defaultOrderMapping = null) {
		if (is_null($defaultOrderMapping)) {
			$defaultOrderMapping = array();
		}
		if (is_null($orderAndLimitHolder)) {
			$orderMapping = $defaultOrderMapping;
		} else {
			$orderMapping = count($orderAndLimitHolder->getOrderMapping()) > 0 ? $orderAndLimitHolder->getOrderMapping() : $defaultOrderMapping;
		}

		foreach ($orderMapping as $orderField => $orderDir) {
			$queryBuilder->addOrderBy($orderField, $orderDir);
		}
	}

	/**
	 * @param callback $func
	 * @throws Exception
	 * @return mixed
	 *
	 * @deprecated use EntityManagerHolder::transactional() instead
	 */
	public function doInTransaction($func) {
		$this->getLog()->info('Begin transaction.');
		$this->getEntityManager()->beginTransaction();
		try {
			$result = $func($this, $this->getLog());

			$this->getLog()->info('Transaction committing...');

			$this->getEntityManager()->commit();
			$this->getEntityManager()->flush();

			$this->getLog()->info('Transaction committed!');
			return $result;
		} catch (Exception $e) {
			$this->getLog()->warn('Exception caught, rolling back!', $e);
			$this->getEntityManager()->rollBack();
			throw $e;
		}
	}

	/**
	 * @param Query $q
	 * @param IOrderAndLimitHolder $orderAndLimitHolder
	 * @return array|Paginator
	 */
	protected function getPaginatedOrderedResultFromQuery(Query $q, IOrderAndLimitHolder $orderAndLimitHolder) {
		if (!is_null($orderAndLimitHolder) && $orderAndLimitHolder->getPageSize() > 0) {
			$q->setFirstResult($orderAndLimitHolder->getCurrentPage() * $orderAndLimitHolder->getPageSize());
			$q->setMaxResults($orderAndLimitHolder->getPageSize());
			$paginator = new Paginator($q, $this->useWhereInPagination());
			$paginator->setUseOutputWalkers(false);
			$orderAndLimitHolder->setResultCount(count($paginator));
			return $paginator;
		} else {
			return $q->getResult($q->getHydrationMode());
		}
	}

	/**
	 * @return bool
	 */
	protected function useWhereInPagination() {
		return true;
	}
}
