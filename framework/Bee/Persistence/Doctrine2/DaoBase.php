<?php
namespace Bee\Persistence\Doctrine2;
use Bee_Persistence_IOrderAndLimitHolder;
use Bee_Persistence_IRestrictionHolder;
use Bee_Utils_Strings;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Logger;

/**
 * User: mp
 * Date: 05.05.13
 * Time: 17:26
 */
class DaoBase extends EntityManagerHolder {

	/**
	 * @var Logger
	 */
	protected $log;

	/**
	 * @return Logger
	 */
	protected function getLog() {
		if (!$this->log) {
			$this->log = Logger::getLogger(get_class($this));
		}
		return $this->log;
	}

	/**
	 * @param QueryBuilder $queryBuilder
	 * @param Bee_Persistence_IRestrictionHolder $restrictionHolder
	 * @param Bee_Persistence_IOrderAndLimitHolder $orderAndLimitHolder
	 * @param array $defaultOrderMapping
	 * @param null $hydrationMode
	 * @return array
	 */
    public function executeListQuery(QueryBuilder $queryBuilder, Bee_Persistence_IRestrictionHolder $restrictionHolder = null, Bee_Persistence_IOrderAndLimitHolder $orderAndLimitHolder = null, array $defaultOrderMapping, $hydrationMode = null) {
        $this->applyFilterRestrictions($queryBuilder, $restrictionHolder);
        $this->applyOrderAndLimit($queryBuilder, $orderAndLimitHolder, $defaultOrderMapping);
        return $this->getQueryFromBuilder($queryBuilder)->execute(null, $hydrationMode);
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
	 * @param Bee_Persistence_IRestrictionHolder $restrictionHolder
	 * @internal param QueryBuilder $query
	 */
    protected final function applyFilterRestrictions(QueryBuilder &$queryBuilder, Bee_Persistence_IRestrictionHolder $restrictionHolder = null) {
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

			$tokenName = 'filtertoken'.$no;
			$params[$tokenName] = '%'.$token.'%';

            foreach ($restrictionHolder->getFilterableFields() as $fieldName) {
                // $fieldName MUST BE A DOCTRINE NAME
                if (Bee_Utils_Strings::hasText($andWhereString)) {
                    $andWhereString .= ' OR ';
                }

                $andWhereString .= $fieldName.' LIKE :'.$tokenName;
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
	 * @param Bee_Persistence_IOrderAndLimitHolder $orderAndLimitHolder
	 * @param array $defaultOrderMapping
	 */
    protected final function applyOrderAndLimit(QueryBuilder &$queryBuilder, Bee_Persistence_IOrderAndLimitHolder $orderAndLimitHolder = null, array $defaultOrderMapping = array()) {
        if (is_null($orderAndLimitHolder)) {
            $orderMapping = $defaultOrderMapping;
        } else {
            $orderMapping = count($orderAndLimitHolder->getOrderMapping()) > 0 ? $orderAndLimitHolder->getOrderMapping() : $defaultOrderMapping;
        }

        foreach ($orderMapping as $orderField => $orderDir) {
            $queryBuilder->addOrderBy($orderField, $orderDir);
        }

        if (is_null($orderAndLimitHolder)) {
            return;
        }

        if ($orderAndLimitHolder->getPageSize() > 0) {
            $queryBuilder->setMaxResults($orderAndLimitHolder->getPageSize());

            // TODO: build a performant count-query! This is simply bullshit!
            $pageCount = ceil(count($this->getQueryFromBuilder($queryBuilder)->execute()) / $orderAndLimitHolder->getPageSize());
            $orderAndLimitHolder->setPageCount($pageCount);

            if ($orderAndLimitHolder->getCurrentPage() > $pageCount) {
                $orderAndLimitHolder->setCurrentPage($pageCount);
            }
            $queryBuilder->setFirstResult($orderAndLimitHolder->getCurrentPage() * $orderAndLimitHolder->getPageSize());
            $queryBuilder->setMaxResults($orderAndLimitHolder->getPageSize());
        }
    }

	/**
	 * @param callback $func
	 * @throws Exception
	 * @return mixed
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
}
