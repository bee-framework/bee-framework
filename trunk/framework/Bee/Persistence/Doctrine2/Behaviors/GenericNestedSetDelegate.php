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
	 */
	public function __construct(EntityManager $entityManager, $entityName) {
		parent::__construct($entityName);
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
		if(is_null($result->lft)) {
			$result->lft = 1;
			$result->lvl = 0;
		}
		return $result;
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
	 * @param mixed $restriction
	 */
	public function setPosition($nestedSetEntity, NodeInfo $nodeInfo, $newLft = false, $newLvl = false, $restriction = false) {
		$bw = new \Bee_Beans_BeanWrapper($nestedSetEntity);
		$bw->setPropertyValue($this->leftFieldName, $nodeInfo->lft);
		$bw->setPropertyValue($this->rightFieldName, $nodeInfo->rgt);
		$bw->setPropertyValue($this->levelFieldName, $nodeInfo->lvl);
		// todo: implement the other cases (i.e. for the non-tree strategy API)
	}

	/**
	 * @param mixed $nestedSetEntity
	 * @param int $delta
	 * @param int $lowerBoundIncl
	 * @param int $upperBoundExcl
	 * @param mixed $restriction
	 */
	public function shift($nestedSetEntity, $delta, $lowerBoundIncl, $upperBoundExcl, $restriction = false) {
		$this->buildShiftQuery($this->leftFieldName, $delta, $lowerBoundIncl, $upperBoundExcl, $restriction)->execute();
		$this->buildShiftQuery($this->rightFieldName, $delta, $lowerBoundIncl, $upperBoundExcl, $restriction)->execute();
		// todo: implement the other cases (i.e. for the non-tree strategy API)
	}

	/**
	 * @param string $fieldName
	 * @param int $delta
	 * @param int $lowerBoundIncl
	 * @param int $upperBoundExcl
	 * @param mixed $restriction
	 * @return \Doctrine\ORM\Query
	 */
	protected function buildShiftQuery($fieldName, $delta, $lowerBoundIncl, $upperBoundExcl, $restriction = false) {
		return $this->getEntityManager()->createQueryBuilder()->update($this->getEntityName(), 'e')
				->set('e.'.$fieldName, 'e.'.$fieldName .' + :delta')->setParameter('delta', $delta)
				->where('e.'.$fieldName . ' >= :lbIncl')->setParameter('lbIncl', $lowerBoundIncl)->getQuery();
//				->orderBy('e.'.$fieldName, $delta > 0 ? 'DESC' : 'ASC')->getQuery();
	}
}
