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
use Bee\Persistence\Behaviors\NestedSet\IDelegate;
use Bee\Persistence\Behaviors\NestedSet\ITreeNode;
use Bee\Persistence\Behaviors\NestedSet\NodeInfo;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

/**
 * User: mp
 * Date: 21.06.13
 * Time: 16:01
 */

class GenericNestedSetDelegate extends DelegateBase implements IDelegate {

	/**
	 * @var string
	 */
	private $leftFieldName = 'lft';

	/**
	 * @var string
	 */
	private $rightFieldName = 'rgt';

	/**
	 * @var string
	 */
	private $levelFieldName = 'lvl';

	/**
	 * @param EntityManager $entityManager
	 * @param string $entityName
	 * @param array $rootKeyFields
	 */
	public function __construct(EntityManager $entityManager, $entityName, array $rootKeyFields = array('rootId')) {
		parent::__construct($entityName, $rootKeyFields);
		$this->setEntityManager($entityManager);
	}

	/**
	 * @param string $leftFieldName
	 */
	public function setLeftFieldName($leftFieldName) {
		$this->leftFieldName = $leftFieldName;
	}

	/**
	 * @param string $levelFieldName
	 */
	public function setLevelFieldName($levelFieldName) {
		$this->levelFieldName = $levelFieldName;
	}

	/**
	 * @param string $rightFieldName
	 */
	public function setRightFieldName($rightFieldName) {
		$this->rightFieldName = $rightFieldName;
	}

	/**
	 * @param mixed $nestedSetEntity
	 * @param mixed $restriction
	 * @return NodeInfo
	 */
	public function getNodeInfo($nestedSetEntity, $restriction = false) {
		$bw = new \Bee_Beans_BeanWrapper($nestedSetEntity);
		$result = new NodeInfo();
		$result->lft = $bw->getPropertyValue($this->leftFieldName);
		$result->rgt = $bw->getPropertyValue($this->rightFieldName);
		$result->lvl = $bw->getPropertyValue($this->levelFieldName);
		if (is_null($result->lft)) {
			$result->lft = 1;
			$result->lvl = 0;
		}
		$result->setGroupKey($this->extractGroupKey($bw));
		return $result;
	}

	/**
	 * @param mixed|\Bee_Beans_BeanWrapper $nestedSetEntityOrBeanWrapper
	 * @return array
	 */
	protected function extractGroupKey($nestedSetEntityOrBeanWrapper) {
		if (!($nestedSetEntityOrBeanWrapper instanceof \Bee_Beans_BeanWrapper)) {
			$nestedSetEntityOrBeanWrapper = new \Bee_Beans_BeanWrapper($nestedSetEntityOrBeanWrapper);
		}
		$groupKey = array();
		foreach ($this->getGroupKeyFields() as $groupFieldName) {
			$groupKey[$groupFieldName] = $nestedSetEntityOrBeanWrapper->getPropertyValue($groupFieldName);
		}
		return $groupKey;
	}

	/**
	 * @param mixed $nestedSetEntity
	 * @param mixed $restriction
	 * @return NodeInfo
	 */
	public function getTreeInfo($nestedSetEntity, $restriction = false) {
		// TODO: Implement getTreeInfo() method.
	}

	/**
	 * @param mixed $nestedSetEntity
	 * @param NodeInfo $nodeInfo
	 * @param bool|int $newLft
	 * @param bool|int $newLvl
	 */
	public function setPosition($nestedSetEntity, NodeInfo $nodeInfo, $newLft = false, $newLvl = false) {
		$bw = new \Bee_Beans_BeanWrapper($nestedSetEntity);
		$bw->setPropertyValue($this->leftFieldName, $nodeInfo->lft);
		$bw->setPropertyValue($this->rightFieldName, $nodeInfo->rgt);
		$bw->setPropertyValue($this->levelFieldName, $nodeInfo->lvl);

		$grpKey = $nodeInfo->getGroupKey();
		foreach ($this->getGroupKeyFields() as $groupKeyField) {
			$bw->setPropertyValue($groupKeyField, $grpKey[$groupKeyField]);
		}
		// todo: implement the other cases (i.e. for the non-tree strategy API)
	}

	/**
	 * @param NodeInfo $parentNodeInfo
	 * @return void
	 */
	public function unsetChildGroupKeys(NodeInfo $parentNodeInfo) {
		$qb = $this->createUpdateBaseQueryBuilder($parentNodeInfo->getGroupKey());

		// set group key to null...
		foreach ($this->getGroupKeyFields() as $groupKeyField) {
			$qb->set("e.$groupKeyField", 'NULL');
		}

		// .. on all children of the parent node given by the NodeInfo
		$qb->andWhere("e.{$this->leftFieldName} > :parentLft")
				->setParameter('parentLft', $parentNodeInfo->lft)
				->andWhere("e.{$this->rightFieldName} < :parentRgt")
				->setParameter('parentRgt', $parentNodeInfo->rgt);
		$qb->getQuery()->execute();
	}

	/**
	 * @param mixed $nestedSetEntity
	 * @param int $delta
	 * @param int $lowerBoundIncl
	 * @param int $upperBoundExcl
	 * @param array $groupKey
	 */
	public function shift($nestedSetEntity, $delta, $lowerBoundIncl, $upperBoundExcl, array $groupKey) {
		$this->buildShiftQuery($this->leftFieldName, $delta, $lowerBoundIncl, $upperBoundExcl, $groupKey)->execute();
		$this->buildShiftQuery($this->rightFieldName, $delta, $lowerBoundIncl, $upperBoundExcl, $groupKey)->execute();
		// todo: implement the other cases (i.e. for the non-tree strategy API)
	}

	/**
	 * @param QueryBuilder $qb
	 * @param NodeInfo $rootNodeInfo
	 * @param string $rootEntityAlias
	 * @param bool $maxLvl
	 */
	public function augmentQueryWithSubtreeLimits(QueryBuilder $qb, NodeInfo $rootNodeInfo, $rootEntityAlias = 'e', $maxLvl = false) {
		if ($rootEntityAlias) {
			$rootEntityAlias = $rootEntityAlias . '.';
		}

		// limit to subtree of this root node
		$qb->andWhere("$rootEntityAlias{$this->leftFieldName} >= :limitLft")
				->setParameter('limitLft', $rootNodeInfo->lft)
				->andWhere("$rootEntityAlias{$this->rightFieldName} <= :limitRgt")
				->setParameter('limitRgt', $rootNodeInfo->rgt);

		// make sure we get only results from current group
		$this->augmentQueryWithGroupLimits($qb, $rootNodeInfo->getGroupKey());

		// apply max level restriction if needed
		if ($maxLvl) {
			$qb->andWhere("$rootEntityAlias{$this->leftFieldName} <= :maxLvl")->setParameter('maxLvl', $maxLvl);
		}

		// proper ordering
		$qb->orderBy("$rootEntityAlias{$this->leftFieldName}", 'ASC');
	}

	/**
	 * @param string $fieldName
	 * @param int $delta
	 * @param int $lowerBoundIncl
	 * @param int $upperBoundExcl
	 * @param array $groupKey
	 * @return \Doctrine\ORM\Query
	 */
	protected function buildShiftQuery($fieldName, $delta, $lowerBoundIncl, $upperBoundExcl, array $groupKey) {
		$qb = $this->createUpdateBaseQueryBuilder($groupKey);
		$qb->set("e.$fieldName", "e.$fieldName + :delta")->setParameter('delta', $delta)
				->andWhere("e.$fieldName >= :lbIncl")->setParameter('lbIncl', $lowerBoundIncl);
		return $qb->getQuery();
	}

	protected function createUpdateBaseQueryBuilder(array $groupKey) {
		$qb = $this->getEntityManager()->createQueryBuilder()->update($this->getEntityName(), 'e');
		return $this->augmentQueryWithGroupLimits($qb, $groupKey);
	}

	protected function augmentQueryWithGroupLimits(QueryBuilder $qb, array $groupKey) {
		foreach ($this->getGroupKeyFields() as $groupFieldName) {
			$qb->andWhere("e.$groupFieldName = :$groupFieldName")->setParameter($groupFieldName, $groupKey[$groupFieldName]);
		}
		return $qb;
	}
}
