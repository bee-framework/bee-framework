<?php
namespace Bee\Persistence\Behaviors\NestedSet;
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

/**
 * User: mp
 * Date: 25.06.13
 * Time: 21:05
 */
 
class DelegateMock implements IDelegate {

	public $protocol = array();

	/**
	 * @var NodeInfo
	 */
	private $nodeInfo;

	public function __construct($lft = 1, $rgt = false, $lvl = 0) {
		$this->nodeInfo = new NodeInfo(array(NodeInfo::LEFT_KEY => $lft, NodeInfo::LEVEL_KEY => $lvl, NodeInfo::RIGHT_KEY => $rgt));
	}

	/**
	 * @param mixed $nestedSetEntity
	 * @param mixed $restriction
	 * @return NodeInfo
	 */
	public function getNodeInfo($nestedSetEntity, $restriction = false) {
		return $this->nodeInfo;
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
		array_push($this->protocol, sprintf('id=%s; lft=%d; rgt=%d; lvl=%d; grp=[%s]', $nestedSetEntity->getId(), $nodeInfo->lft, $nodeInfo->rgt, $nodeInfo->lvl, implode(',', $nodeInfo->getGroupKey())));
	}

	/**
	 * @param mixed $nestedSetEntity
	 * @param int $delta
	 * @param int $lowerBoundIncl
	 * @param int $upperBoundExcl
	 * @param array $groupKey
	 */
	public function shift($nestedSetEntity, $delta, $lowerBoundIncl, $upperBoundExcl, array $groupKey) {
		array_push($this->protocol, sprintf('delta=%d; %d<=lft/rgt%s; grp=[%s]', $delta, $lowerBoundIncl, $upperBoundExcl !== false ? '<'.$upperBoundExcl : '', implode(',', $groupKey)));
	}

	/**
	 * @param NodeInfo $parentNodeInfo
	 * @return void
	 */
	public function unsetChildGroupKeys(NodeInfo $parentNodeInfo) {
		// TODO: Implement unsetChildGroupKeys() method.
	}
}
