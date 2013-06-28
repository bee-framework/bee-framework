<?php
namespace Treetest;
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
use Bee\Persistence\Behaviors\NestedSet\TreeStrategy as NestedSetStrategy;
use Bee\Persistence\Doctrine2\DaoBase;
use Bee\Persistence\Doctrine2\Behaviors\GenericNestedSetDelegate;
use Doctrine\ORM\EntityManager;

/**
 * User: mp
 * Date: 27.06.13
 * Time: 04:35
 */
 
class TreeDao extends DaoBase {

	const ENTITY_CLASS_NAME = 'Treetest\Node';

	/**
	 * @var NestedSetStrategy
	 */
	private $nestedSetStrategy;

	/**
	 * @var GenericNestedSetDelegate
	 */
	private $nestedSetDelegate;

	public function __construct(EntityManager $entityManager) {
		$this->setEntityManager($entityManager);
		$this->nestedSetDelegate = new GenericNestedSetDelegate($entityManager, self::ENTITY_CLASS_NAME);
		$this->nestedSetStrategy = new NestedSetStrategy($this->nestedSetDelegate);
	}

	/**
	 * @return \Bee\Persistence\Behaviors\NestedSet\Strategy
	 */
	public function getNestedSetStrategy() {
		return $this->nestedSetStrategy;
	}

	public function loadTree($rootNodeIdOrEntity) {
		if(!$rootNodeIdOrEntity instanceof Node) {
			$rootNodeIdOrEntity = $this->getEntityManager()->find(self::ENTITY_CLASS_NAME, $rootNodeIdOrEntity);
		}

		// obtain NodeInfo (left / right boundaries + group info) for the tree part rooted at given node
		$rootNodeInfo = $this->nestedSetDelegate->getNodeInfo($rootNodeIdOrEntity);

		// construct our base query
		$qb = $this->getEntityManager()->createQueryBuilder()->select('e')->from(self::ENTITY_CLASS_NAME, 'e');

		// augment query with subtree limits
		$this->nestedSetDelegate->augmentQueryWithSubtreeLimits($qb, $rootNodeInfo);

		// execute query and create tree structure from result
		return $this->nestedSetStrategy->buildTreeStructure($qb->getQuery()->execute());
	}
}
