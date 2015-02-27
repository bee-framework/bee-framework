<?php
namespace Bee\Persistence\Doctrine2;

use Bee\Persistence\IOrderAndLimitHolder;
use Bee\Persistence\IRestrictionHolder;
use Bee\Utils\Assert;
use Bee\Utils\Strings;
use Doctrine\ORM\QueryBuilder;
use UnexpectedValueException;

/**
 * Class GenericDaoBase
 * @package Bee\Persistence\Doctrine2
 */
abstract class GenericDaoBase extends DaoBase {

	const ALIAS_MATCHER = '#^([a-zA-Z0-9_]{2,})\.#';

	/**
	 * @var callable
	 */
	private $idRestrictor;

	/**
	 * @var array
	 */
	private $aliases;

    /**
     * @var array
     */
    private $reverseAliases;

	/**
	 * @var array
	 *
	 * todo: this is only for one-time use during a request. should be ok for MOST cases...
	 */
	private $addedAliases = array();

	/**
	 * @var array
	 */
	private $joins = array();

	/**
	 * @var array
	 */
	private $restrictions = array();

	/**
	 * @var array
	 */
	private $defaultOrderMapping = array();

	/**
	 * @param mixed $id
	 * @throws UnexpectedValueException
	 * @return mixed
	 */
	public function getById($id) {
		if(!is_callable($this->idRestrictor)) {
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

				$where = implode(' AND ', $whereParts);
				$this->idRestrictor = function(QueryBuilder $qb, $id) use ($where) {
					$qb->where($where)->setParameters($id);
				};
			} else {
				$where = $baseEntityAlias . '.' . $idFields . ' = :id';
				$this->idRestrictor = function(QueryBuilder $qb, $id) use ($where) {
					$qb->where($where)->setParameter('id', $id);
				};
			}
		}

		$setter = $this->idRestrictor;
		$setter($qb = $this->getBaseQuery(), $id);
		return $this->getSingleResult($qb);
	}

	/**
	 * @param IRestrictionHolder $restrictionHolder
	 * @param IOrderAndLimitHolder $orderAndLimitHolder
	 * @param array $defaultOrderMapping
	 * @return array
	 */
	public function getList(IRestrictionHolder $restrictionHolder = null, IOrderAndLimitHolder $orderAndLimitHolder = null, array $defaultOrderMapping = null) {
		return $this->executeListQuery($this->getBaseQuery(), $restrictionHolder, $orderAndLimitHolder, $defaultOrderMapping, null);
	}

    /**
     * @param QueryBuilder $queryBuilder
     * @param string $expr
     * @return string
     */
	protected function transformAndAddAliasForPathExpression(QueryBuilder $queryBuilder, $expr) {
        $tokens = explode('.', $expr);
        $currentAlias = $this->getEntityAlias();
        do {
            $field = $currentAlias . '.' . array_shift($tokens);
            if(array_key_exists($field, $this->reverseAliases)) {
                // this is an association, there must be an actual field token left in the array
                Assert::isTrue(count($tokens) > 0, 'No more property path tokens - check your listConfiguration for invalid property paths!');
                $currentAlias = $this->reverseAliases[$field];
            }

        } while(count($tokens) > 0);

        $this->addAliasForExpression($queryBuilder, $field);
        return $field;
	}

    /**
     * @param QueryBuilder $queryBuilder
     * @param string $expr
     */
	protected function addAliasForExpression(QueryBuilder $queryBuilder, $expr) {
		if(preg_match(self::ALIAS_MATCHER, $expr, $matches)) {
			$this->addAlias($queryBuilder, $matches[1]);
		}
	}

	/**
	 * @param QueryBuilder $queryBuilder
	 * @param string $alias
	 */
	protected function addAlias(QueryBuilder $queryBuilder, $alias) {
		if(!$this->containsAlias($alias)) {
			$this->addedAliases[$alias] = true;
			$this->addAliasForExpression($queryBuilder, $this->aliases[$alias]);
			$queryBuilder->leftJoin($this->aliases[$alias], $alias);
		}
	}

	/**
	 * @param $alias
	 * @return boolean
	 */
	protected function containsAlias($alias) {
		// todo: Alias presence could in theory also be detected by examining the query builders DQL parts. Feasibility / performance?
		// pros: more thorough and consistent
		// cons: more overhead?
		return $alias == $this->getEntityAlias() || array_key_exists($alias, $this->addedAliases) || array_key_exists($alias, $this->getJoins());
	}

	public function executeListQuery(QueryBuilder $queryBuilder, IRestrictionHolder $restrictionHolder = null, IOrderAndLimitHolder $orderAndLimitHolder = null, array $defaultOrderMapping = null, $hydrationMode = null) {
        // todo: there is no particular reason to do the path-to-alias transformations in every request...
		if(!is_null($restrictionHolder)) {
            $internalFilterableFields = array();
			if(Strings::hasText($restrictionHolder->getFilterString())) {
				foreach($restrictionHolder->getFilterableFields() as $field) {
                    $internalFilterableFields[] = $this->transformAndAddAliasForPathExpression($queryBuilder, $field);
				}
			}
            $internalFilters = array();
			if(count($restrictionHolder->getFilters()) > 0) {
            	foreach($restrictionHolder->getFilters() as $field => $value) {
                    $internalFilters[$this->transformAndAddAliasForPathExpression($queryBuilder, $field)] = $value;
				}
			}
            $restrictionHolder = new GenericDaoBase_RestrictionWrapper($restrictionHolder, $internalFilterableFields, $internalFilters);
		}

		if(!is_null($orderAndLimitHolder)) {
			if(count($orderAndLimitHolder->getOrderMapping()) > 0) {
                $internalMapping = array();
				foreach($orderAndLimitHolder->getOrderMapping() as $field => $dir) {
                    $internalMapping[$this->transformAndAddAliasForPathExpression($queryBuilder, $field)] = $dir;
				}

                $orderAndLimitHolder = new GenericDaoBase_OrderAndLimitWrapper($orderAndLimitHolder, $internalMapping);
			}
		}

		return parent::executeListQuery($queryBuilder, $restrictionHolder, $orderAndLimitHolder, $defaultOrderMapping ?: $this->getDefaultOrderMapping(), $hydrationMode ?: $this->getHydrationMode());
	}

    /**
     * @param null $entity
     * @return QueryBuilder
     */
	protected function getBaseQuery($entity = null) {
		$baseEntityAlias = $this->getEntityAlias();
        $entity = $entity ?: $this->getEntity();
//		$indexBy = count($this->getIdFieldName()) > 1 ? null : $baseEntityAlias . '.' . $this->getIdFieldName();
//		return $this->getEntityManager()->createQueryBuilder()->select($baseEntityAlias)
//				->from($this->getEntity(), $baseEntityAlias, $indexBy);
		$qb = $this->getEntityManager()->createQueryBuilder()->select($baseEntityAlias)->from($entity, $baseEntityAlias, $this->getIndexBy());
		$this->addJoinsToBaseQuery($qb);
		$this->addRestrictionsToBaseQuery($qb);
		return $qb;
	}

	/**
	 * @param QueryBuilder $q
	 */
	protected function addJoinsToBaseQuery(QueryBuilder $q) {
		foreach($this->joins as $alias => $relation) {
			$q->addSelect($alias)->leftJoin($relation, $alias);
		}
	}

	/**
	 * @param QueryBuilder $q
	 */
	protected function addRestrictionsToBaseQuery(QueryBuilder $q) {
		foreach($this->restrictions as $restriction) {
			$q->andWhere($restriction);
		}
	}

	/**
	 * @param QueryBuilder $qb
	 * @return mixed
	 */
	protected function getSingleResult(QueryBuilder $qb) {
		$q = $this->getQueryFromBuilder($qb);
		return $q->getSingleResult($this->getHydrationMode());
	}

	/**
	 * @return null|string
	 */
	protected function getHydrationMode() {
		return null;
	}

	/**
	 * @return string
	 */
	protected function getEntityAlias() {
		return 'e';
	}

	/**
	 * @return mixed
	 */
	abstract protected function getIdFieldName();

	/**
	 * @return string
	 */
	public abstract function getEntity();

	/**
	 * @return null
	 */
	protected function getIndexBy() {
		return null;
	}

    /**
     * @param QueryBuilder $queryBuilder
     * @param $filters
     * @param $categoryName
     * @param string $baseAlias
     * @param null $property
     */
    protected function addCategoryRestrictions(QueryBuilder $queryBuilder, $filters, $categoryName, $baseAlias = 'e', $property = null) {
        $property = $property ?: $categoryName;
        if (array_key_exists($categoryName, $filters)) {
            if(!is_array($catIds = $filters[$categoryName])) {
                $catIds = array_filter(explode(',', $catIds));
            }
            if(count($catIds) > 0) {
                $this->addAlias($queryBuilder, $baseAlias);
                $queryBuilder->andWhere($queryBuilder->expr()->in('IDENTITY(' . $baseAlias . '.' . $property . ')', $catIds));
            }
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param $filters
     * @param string $fldName
     */
    protected function addValueRestriction(QueryBuilder $queryBuilder, $filters, $fldName) {
        if (array_key_exists($fldName, $filters) && $value = $filters[$fldName]) {
            $fldName = $this->transformAndAddAliasForPathExpression($queryBuilder, $fldName);
            $queryBuilder->andWhere($fldName . ' = :val')->setParameter('val', $value);
        }
    }

    // =================================================================================================================
	// == GETTERS & SETTERS ============================================================================================
	// =================================================================================================================

	/**
	 * @return array
	 */
	public function getAliases() {
		return $this->aliases;
	}

	/**
	 * @param array $aliases
	 */
	public function setAliases(array $aliases) {
		$this->aliases = $aliases;
        $this->reverseAliases = array_flip($aliases);
	}

	/**
	 * @param array $joins
	 */
	public function setJoins(array $joins) {
		$this->joins = $joins;
	}

	/**
	 * @return array
	 */
	public function getJoins() {
		return $this->joins;
	}

	/**
	 * @param array $restrictions
	 */
	public function setRestrictions(array $restrictions) {
		$this->restrictions = $restrictions;
	}

	/**
	 * @return array
	 */
	public function getDefaultOrderMapping() {
		return $this->defaultOrderMapping;
	}

	/**
	 * @param array $defaultOrderMapping
	 */
	public function setDefaultOrderMapping(array $defaultOrderMapping) {
		$this->defaultOrderMapping = $defaultOrderMapping;
	}
}

class GenericDaoBase_OrderAndLimitWrapper implements IOrderAndLimitHolder {

    /**
     * @var IOrderAndLimitHolder
     */
    private $wrappedOrderAndLimitHolder;

    /**
     * @var array
     */
    private $internalOrderMapping;

    function __construct(IOrderAndLimitHolder $wrappedOrderAndLimitHolder, $internalOrderMapping) {
        $this->wrappedOrderAndLimitHolder = $wrappedOrderAndLimitHolder;
        $this->internalOrderMapping = $internalOrderMapping;
    }

    /**
     * @return array
     */
    public function getOrderMapping() {
        return $this->internalOrderMapping;
    }

    /**
     * @return int
     */
    public function getPageSize() {
        return $this->wrappedOrderAndLimitHolder->getPageSize();
    }

    /**
     * @return int
     */
    public function getPageCount() {
        return $this->wrappedOrderAndLimitHolder->getPageCount();
    }

    /**
     * @return int
     */
    public function getCurrentPage() {
        return $this->wrappedOrderAndLimitHolder->getCurrentPage();
    }

    /**
     * @param $currentPage
     */
    public function setCurrentPage($currentPage) {
        $this->wrappedOrderAndLimitHolder->setCurrentPage($currentPage);
    }

    /**
     * @param int $resultCount
     */
    public function setResultCount($resultCount) {
        $this->wrappedOrderAndLimitHolder->setResultCount($resultCount);
    }
}

class GenericDaoBase_RestrictionWrapper implements IRestrictionHolder {

    /**
     * @var IRestrictionHolder
     */
    private $wrappedRestrictionHolder;

    /**
     * @var array
     */
    private $internalFilterableFields;

    /**
     * @var array
     */
    private $internalFilters;

    /**
     * @param $wrappedRestrictionHolder
     * @param $internalFilterableFields
     * @param $internalFilters
     */
    function __construct(IRestrictionHolder $wrappedRestrictionHolder, array $internalFilterableFields, array $internalFilters) {
        $this->wrappedRestrictionHolder = $wrappedRestrictionHolder;
        $this->internalFilterableFields = $internalFilterableFields;
        $this->internalFilters = $internalFilters;
    }

    /**
     * @return array
     */
    public function getFilterableFields() {
        return $this->internalFilterableFields;
    }

    /**
     * @return array
     */
    public function getFilterString() {
        return $this->wrappedRestrictionHolder->getFilterString();
    }

    /**
     * @return array
     */
    public function getFilters() {
        return $this->internalFilters;
    }
}