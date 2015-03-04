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

    /**
     * @var callable
     */
    private $idRestrictor;

    /**
     * @var array
     *
     * ex:
     *  e0 => 'e.adresse',
     *  e1 => 'e.hochschultyp',
     *  e2 => 'e.adresse.land',
     *  e3 => 'e.adresse.verwaltungseinheit',
     *  e4 => 'e.hochschultyp.kategorie',
     *  e5 => 'e.adresse.land.regionen'
     */
    private $aliases;

    /**
     * @var array
     *
     * ex:
     *  'e.adresse'                     => e0
     *  'e.hochschultyp'                => e1
     *  'e.adresse.land',               => e2
     *  'e.adresse.verwaltungseinheit'  => e3
     *  'e.hochschultyp.kategorie'      => e4
     *  'e.adresse.land.regionen'       => e5
     */
    private $reverseAliases;

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
     * @var array
     */
    private $fieldDisaggregations = array();

    /**
     * @var array
     */
    private $addedAliases = array();

    /**
     * @param mixed $id
     * @throws UnexpectedValueException
     * @return mixed
     */
    public function getById($id) {
        if (!is_callable($this->idRestrictor)) {
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
                /** @noinspection PhpUnusedParameterInspection */
                array_walk($id, function ($value, $key) use ($baseEntityAlias, &$whereParts) {
                    $whereParts[] = $baseEntityAlias . '.' . $key . ' = ' . ':' . $key;
                });

                $where = implode(' AND ', $whereParts);
                $this->idRestrictor = function (QueryBuilder $qb, $id) use ($where) {
                    $qb->where($where)->setParameters($id);
                };
            } else {
                $where = $baseEntityAlias . '.' . $idFields . ' = :id';
                $this->idRestrictor = function (QueryBuilder $qb, $id) use ($where) {
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
     * @param IRestrictionHolder $restrictionHolder
     * @param IOrderAndLimitHolder $orderAndLimitHolder
     * @param array $defaultOrderMapping
     * @param null $hydrationMode
     * @return array
     */
    public function executeListQuery(QueryBuilder $queryBuilder, IRestrictionHolder $restrictionHolder = null, IOrderAndLimitHolder $orderAndLimitHolder = null, array $defaultOrderMapping = null, $hydrationMode = null) {
        if (!is_null($restrictionHolder)) {
            $internalFilterableFields = array();
            if (Strings::hasText($restrictionHolder->getFilterString())) {
                $this->disaggregateAndInternalizeFieldList($restrictionHolder->getFilterableFields(), $internalFilterableFields, $queryBuilder);
            }
//            $internalFilters = array();
//            if (count($restrictionHolder->getFilters()) > 0) {
//                $this->internalizeFieldValueMapping($restrictionHolder->getFilters(), $internalFilters, $queryBuilder);
//            }
            $restrictionHolder = new GenericDaoBase_RestrictionWrapper($restrictionHolder, $internalFilterableFields);
        }

        if (!is_null($orderAndLimitHolder)) {
            if (count($orderAndLimitHolder->getOrderMapping()) > 0) {
                $internalMapping = array();
                $this->disaggregateAndInternalizeFieldValueMapping($orderAndLimitHolder->getOrderMapping(), $internalMapping, $queryBuilder);
                $orderAndLimitHolder = new GenericDaoBase_OrderAndLimitWrapper($orderAndLimitHolder, $internalMapping);
            }
        }

        return parent::executeListQuery($queryBuilder, $restrictionHolder, $orderAndLimitHolder, $defaultOrderMapping ?: $this->getDefaultOrderMapping(), $hydrationMode ?: $this->getHydrationMode());
    }

    /**
     * @param array $externalFieldValueMapping
     * @param array $internalFieldValueMapping
     * @param QueryBuilder $queryBuilder
     */
    protected final function disaggregateAndInternalizeFieldValueMapping(array $externalFieldValueMapping, array &$internalFieldValueMapping, QueryBuilder $queryBuilder) {
        foreach ($externalFieldValueMapping as $field => $value) {
            array_walk($this->getFieldDisaggregation($field), function ($field) use (&$internalFieldValueMapping, $queryBuilder, $value) {
                $internalFieldValueMapping[$this->internalizeFieldExpression($field, $queryBuilder)] = $value;
            });
        }
    }

    /**
     * @param array $externalFieldList
     * @param array $internalFieldList
     * @param QueryBuilder $queryBuilder
     */
    protected final function disaggregateAndInternalizeFieldList(array $externalFieldList, array &$internalFieldList, QueryBuilder $queryBuilder) {
        foreach ($externalFieldList as $field) {
            $internalFieldList = array_merge($internalFieldList, array_map(function ($field) use (&$queryBuilder) {
                return $this->internalizeFieldExpression($field, $queryBuilder);
            }, $this->getFieldDisaggregation($field)));
        }
    }

    /**
     * @param string $fieldExpr
     * @param QueryBuilder $queryBuilder
     * @param bool $join
     * @return string
     */
    protected final function internalizeFieldExpression($fieldExpr, QueryBuilder $queryBuilder, $join = false) {
        // ex: $fieldExpr = 'e.hochschultyp.kategorie.promotionsrecht'
        $dotPos = strrpos($fieldExpr, '.');             // 24
        $fieldName = substr($fieldExpr, $dotPos + 1);   // 'promotionsrecht'
        $pathExpr = substr($fieldExpr, 0, $dotPos);     // 'e.hochschultyp.kategorie'

        if($pathExpr != $this->getEntityAlias()) {
            Assert::isTrue(array_key_exists($pathExpr, $this->reverseAliases), 'Unknown path expression "' . $pathExpr . '"');
            $this->internalizePathExpression($pathExpr, $queryBuilder, $join);
            $pathExpr = $this->reverseAliases[$pathExpr];
        }

        // ex: return e4.promotionsrecht
        return $pathExpr . '.' . $fieldName;
    }

    /**
     * @param string $pathExpr
     * @param QueryBuilder $queryBuilder
     * @param bool $fetchJoin
     * @return string
     */
    protected final function internalizePathExpression($pathExpr, QueryBuilder $queryBuilder, $fetchJoin = false) {
        // ex (Rc1):    $pathExpr = 'e.hochschultyp.kategorie'
        // ex (Rc2):    $pathExpr = 'e.hochschultyp'
        // ex (Rc3):    $pathExpr = 'e'

        if(($dotPos = strrpos($pathExpr, '.')) !== false) {
            $currentAlias = $this->reverseAliases[$pathExpr];

            if(!array_key_exists($currentAlias, $this->addedAliases)) {

                $currentAssociation = substr($pathExpr, $dotPos + 1);
                $pathExpr = substr($pathExpr, 0, $dotPos);

                // ex (Rc1):
                //      $currentAlias = 'e4'
                //      $currentAssociation = 'kategorie'
                //      $pathExpr = 'e.hochschultyp'

                // ex (Rc2):
                //      $currentAlias = 'e1'
                //      $currentAssociation = 'hochschultyp'
                //      $pathExpr = 'e'

                $parentAlias = $this->internalizePathExpression($pathExpr, $queryBuilder, $fetchJoin);

                // ex (Rc2):    $parentAlias = 'e'
                // ex (Rc1):    $parentAlias = 'e1'

                $currentAssociation = $parentAlias . '.' . $currentAssociation;

                // ex (Rc2):    $currentAssociation = 'e.hochschultyp'
                // ex (Rc1):    $currentAssociation = 'e1.kategorie'

                $queryBuilder->leftJoin($currentAssociation, $currentAlias);
                if($fetchJoin) {
                    $queryBuilder->addSelect($currentAlias);
                }
                $this->addedAliases[$currentAlias] = $currentAssociation;

                // ex (Rc2):    $this->addedAliases = array('e1' => 'e.hochschultyp')
                // ex (Rc1):    $this->addedAliases = array('e1' => 'e.hochschultyp', 'e4' => 'e1.kategorie')
            }
            $pathExpr = $currentAlias;
        }

        // ex (Rc3):    $pathExpr = 'e'
        // ex (Rc2):    $pathExpr = 'e1'
        // ex (Rc1):    $pathExpr = 'e4'

        return $pathExpr;
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
        foreach ($this->joins as $join) {
            $this->internalizePathExpression($join, $q, true);
        }
    }

    /**
     * @param QueryBuilder $q
     */
    protected function addRestrictionsToBaseQuery(QueryBuilder $q) {
        foreach ($this->restrictions as $restriction) {
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
     * @param string $fieldPath
     */
    protected function addCategoryRestrictions(QueryBuilder $queryBuilder, $filters, $fieldPath) {
        if (array_key_exists($fieldPath, $filters)) {
            if (!is_array($catIds = $filters[$fieldPath])) {
                $catIds = array_filter(explode(',', $catIds));
            }
            if (count($catIds) > 0) {
                $queryBuilder->andWhere($queryBuilder->expr()->in('IDENTITY(' . $this->internalizeFieldExpression($fieldPath, $queryBuilder) . ')', $catIds));
            }
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array $filters
     * @param string $fieldExpr
     * @param string $filterKey
     */
    protected function addValueRestriction(QueryBuilder $queryBuilder, $filters, $fieldExpr, $filterKey = '') {
        $filterKey = $filterKey ?: $fieldExpr;
        if (array_key_exists($filterKey, $filters) && $value = $filters[$filterKey]) {
            $queryBuilder->andWhere($this->internalizeFieldExpression($fieldExpr, $queryBuilder) . ' = :val')->setParameter('val', $value);
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
        $this->aliases = array();
        array_walk($aliases, function ($val, $key) {
            $this->aliases[is_numeric($key) ? 'e' . $key : $key] = $val;
        });
        $this->reverseAliases = array_flip($this->aliases);
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

    /**
     * @return array
     */
    public function getFieldDisaggregations() {
        return $this->fieldDisaggregations;
    }

    /**
     * @param array $fieldDisaggregations
     */
    public function setFieldDisaggregations($fieldDisaggregations) {
        $this->fieldDisaggregations = $fieldDisaggregations;
    }

    /**
     * @param $aggregateFieldName
     * @return array
     */
    protected function getFieldDisaggregation($aggregateFieldName) {
        // prefix only if simple path expression not prefixed with entity alias and not a function expression
        if(!preg_match('#^(?:' . $this->getEntityAlias() . '\.|\w+\()#', $aggregateFieldName)) {
            $aggregateFieldName = $this->getEntityAlias() . '.' . $aggregateFieldName;
        }
        if (array_key_exists($aggregateFieldName, $this->fieldDisaggregations)) {
            return $this->fieldDisaggregations[$aggregateFieldName];
        }
        return array($aggregateFieldName);
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
     * @param $wrappedRestrictionHolder
     * @param $internalFilterableFields
     */
    function __construct(IRestrictionHolder $wrappedRestrictionHolder, array $internalFilterableFields/*, array $internalFilters*/) {
        $this->wrappedRestrictionHolder = $wrappedRestrictionHolder;
        $this->internalFilterableFields = $internalFilterableFields;
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
        return $this->wrappedRestrictionHolder->getFilters();
    }
}