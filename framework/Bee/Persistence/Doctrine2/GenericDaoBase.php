<?php
namespace Bee\Persistence\Doctrine2;

use Bee\Persistence\IOrderAndLimitHolder;
use Bee\Utils\Assert;
use Bee\Utils\Strings;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use UnexpectedValueException;

/**
 * Class GenericDaoBase
 * @package Bee\Persistence\Doctrine2
 */
abstract class GenericDaoBase extends PaginatingDao {

    const FILTER_STRING = 'filterString';
    const FILTER_STRING_FIELDS = 'filterStringFields';

    const SCALAR_RESTRICTION_EQUAL = ' = ';
    const SCALAR_RESTRICTION_LESS = ' < ';
    const SCALAR_RESTRICTION_LESS_OR_EQUAL = ' <= ';
    const SCALAR_RESTRICTION_GREATER = ' > ';
    const SCALAR_RESTRICTION_GREATER_OR_EQUAL = ' >= ';

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
     * @var int
     */
    protected $scalarParamCount = 0;

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
     * @param mixed $filters
     * @param IOrderAndLimitHolder $orderAndLimitHolder
     * @param array $defaultOrderMapping
     * @param string $hydrationMode
     * @return array
     */
    public function getList($filters = null, IOrderAndLimitHolder $orderAndLimitHolder = null, array $defaultOrderMapping = null, $hydrationMode = null) {
        $qb = $this->getBaseQuery();
        $this->applyFilterRestrictions($qb, $filters);
        return $this->executeListQuery($qb, $orderAndLimitHolder, $defaultOrderMapping ?: $this->getDefaultOrderMapping(), $hydrationMode ?: $this->getHydrationMode());
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

    // =================================================================================================================
    // == Field disaggregation and canonicalization ====================================================================
    // =================================================================================================================

    /**
     * @param QueryBuilder $queryBuilder
     * @param array $orderMapping
     * @return QueryBuilder
     */
    protected function applyOrderMapping(QueryBuilder $queryBuilder, array $orderMapping = array()) {
        return parent::applyOrderMapping($queryBuilder, $this->disaggregateAndInternalizeFieldValueMapping($orderMapping, $queryBuilder));
    }

    /**
     * @param array $externalFieldValueMapping
     * @param array $internalFieldValueMapping
     * @param QueryBuilder $queryBuilder
     * @return array
     */
    protected final function disaggregateAndInternalizeFieldValueMapping(array $externalFieldValueMapping, QueryBuilder $queryBuilder, array &$internalFieldValueMapping = array()) {
        foreach ($externalFieldValueMapping as $field => $value) {
            array_map(function ($field) use (&$internalFieldValueMapping, $queryBuilder, $value) {
                $internalFieldValueMapping[$this->internalizeFieldExpression($field, $queryBuilder)] = $value;
            }, $this->getFieldDisaggregation($field));
        }
        return $internalFieldValueMapping;
    }

    /**
     * @param array $externalFieldList
     * @param QueryBuilder $queryBuilder
     * @param array $internalFieldList
     * @return array
     */
    protected final function disaggregateAndInternalizeFieldList(array $externalFieldList, QueryBuilder $queryBuilder, array &$internalFieldList = array()) {
        foreach ($externalFieldList as $field) {
            $internalFieldList = array_merge($internalFieldList, array_map(function ($field) use (&$queryBuilder) {
                return $this->internalizeFieldExpression($field, $queryBuilder);
            }, $this->getFieldDisaggregation($field)));
        }
        return $internalFieldList;
    }

    /**
     * @param string $fieldExpr
     * @param QueryBuilder $queryBuilder
     * @param bool $join
     * @return string
     */
    protected final function internalizeFieldExpression($fieldExpr, QueryBuilder $queryBuilder, $join = false) {
        // ex: $fieldExpr = 'e.hochschultyp.kategorie.promotionsrecht'
        preg_match('#^(?:([\w.]*)\.)?(.*?)$#', $fieldExpr, $matches);
        $pathExpr = $matches[1];    // 'e.hochschultyp.kategorie'
        $fieldName = $matches[2];   // 'promotionsrecht'

        if ($pathExpr && $pathExpr != $this->getEntityAlias()) {
            Assert::isTrue(array_key_exists($pathExpr, $this->reverseAliases), 'Unknown path expression "' . $pathExpr . '"');
            $this->internalizePathExpression($pathExpr, $queryBuilder, $join);
            $pathExpr = $this->reverseAliases[$pathExpr];
        }

        // ex: return e4.promotionsrecht
        return ($pathExpr ? $pathExpr . '.' : '') . $fieldName;
    }

    /**
     * @param string $pathExpr
     * @param QueryBuilder $queryBuilder
     * @param bool $fetchJoin
     * @return string
     */
    protected final function internalizePathExpression($pathExpr, QueryBuilder $queryBuilder, $fetchJoin = false, $condition = null) {
        // ex (Rc1):    $pathExpr = 'e.hochschultyp.kategorie'
        // ex (Rc2):    $pathExpr = 'e.hochschultyp'
        // ex (Rc3):    $pathExpr = 'e'

        if (($dotPos = strrpos($pathExpr, '.')) !== false) {
            $currentAlias = $this->reverseAliases[$pathExpr];

            if (!array_key_exists($currentAlias, $this->addedAliases)) {

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

                $queryBuilder->leftJoin($currentAssociation, $currentAlias, !is_null($condition) ? Join::WITH : null, !is_null($condition) ? str_replace('{currentAlias}', $currentAlias, $condition) : null);
                if ($fetchJoin) {
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

    // =================================================================================================================
    // == Filtering helpers ============================================================================================
    // =================================================================================================================

    /**
     * Apply the filter restrictions defined by the filters array to the given QueryBuilder.
     *
     * Intended to be overridden by subclasses. The default implementation only applies a string filter if defined.
     *
     * @param QueryBuilder $queryBuilder
     * @param mixed $filters
     * @return QueryBuilder for chaining
     */
    protected function applyFilterRestrictions(QueryBuilder &$queryBuilder, $filters = null) {
        if (!is_null($filters)) {
            if(array_key_exists(self::FILTER_STRING, $filters) && Strings::hasText($filterString = $filters[self::FILTER_STRING])) {
                $this->addStringFilterRestrictions($queryBuilder, $filterString, $filters[self::FILTER_STRING_FIELDS]);
            }
        }
        return $queryBuilder;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string $filterString
     * @param array $filterableFields
     * @return QueryBuilder
     */
    protected function addStringFilterRestrictions(QueryBuilder $queryBuilder, $filterString, array $filterableFields = array()) {
        if (Strings::hasText($filterString)) {
            $filterableFields = $this->disaggregateAndInternalizeFieldList($filterableFields, $queryBuilder);

            $filterTokens = Strings::tokenizeToArray($filterString, ' ');
            foreach ($filterTokens as $no => $token) {
                $andWhereString = '';
                $params = array();

                $tokenName = 'filtertoken' . $no;
                $params[$tokenName] = '%' . $token . '%';
                foreach ($filterableFields as $fieldName) {
                    // $fieldName MUST BE A DOCTRINE NAME
                    if (Strings::hasText($andWhereString)) {
                        $andWhereString .= ' OR ';
                    }

                    $andWhereString .= $fieldName . ' LIKE :' . $tokenName;
                }
                if (Strings::hasText($andWhereString)) {
                    $queryBuilder->andWhere($andWhereString);

                    foreach ($params as $key => $value) {
                        $queryBuilder->setParameter($key, $value);
                    }
                }
            }
        }
        return $queryBuilder;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param $filters
     * @param string $fieldPath
     * @param bool $filterName
     * @return QueryBuilder for chaining
     */
    protected final function addCategoryRestrictions(QueryBuilder $queryBuilder, $filters, $fieldPath, $filterName = false) {
        $filterName = $filterName ?: $fieldPath;
        if (array_key_exists($filterName, $filters)) {
            if (!is_array($catIds = $filters[$filterName])) {
                $catIds = array_filter(explode(',', $catIds));
            }
            if (count($catIds) > 0) {
                $queryBuilder->andWhere($queryBuilder->expr()->in('IDENTITY(' . $this->internalizeFieldExpression($fieldPath, $queryBuilder) . ')', $catIds));
            }
        }
        return $queryBuilder;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array $filters
     * @param string $fieldExpr
     * @param string $filterKey
     * @param string $comp
     * @return QueryBuilder for chaining
     */
    protected final function addScalarRestriction(QueryBuilder $queryBuilder, $filters, $fieldExpr, $filterKey = '', $comp = self::SCALAR_RESTRICTION_EQUAL) {
        $filterKey = $filterKey ?: $fieldExpr;
        if (array_key_exists($filterKey, $filters) && $value = $filters[$filterKey]) {
            $paramName = 'sclr' . $this->scalarParamCount++;
            $queryBuilder->andWhere($this->internalizeFieldExpression($fieldExpr, $queryBuilder) . $comp . ':' . $paramName)->setParameter($paramName, $value);
        }
        return $queryBuilder;
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
        if (!preg_match('#^(?:' . $this->getEntityAlias() . '\.|\w+\()#', $aggregateFieldName)) {
            $aggregateFieldName = $this->getEntityAlias() . '.' . $aggregateFieldName;
        }
        if (array_key_exists($aggregateFieldName, $this->fieldDisaggregations)) {
            return $this->fieldDisaggregations[$aggregateFieldName];
        }
        return array($aggregateFieldName);
    }
}