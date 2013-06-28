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
 * Date: 07.05.13
 * Time: 15:58
 */

interface IDelegate {

	/**
	 * @param mixed $nestedSetEntity
	 * @param mixed $restriction
	 * @return NodeInfo
	 */
	public function getNodeInfo($nestedSetEntity, $restriction = false);

	/**
	 * @param mixed $nestedSetEntity
	 * @param mixed $restriction
	 * @return NodeInfo
	 */
	public function getTreeInfo($nestedSetEntity, $restriction = false);

	/**
	 * @param mixed $nestedSetEntity
	 * @param NodeInfo $nodeInfo
	 * @param bool|int $newLft
	 * @param bool|int $newLvl
	 * @return void
	 */
	public function setPosition($nestedSetEntity, NodeInfo $nodeInfo, $newLft = false, $newLvl = false);

	/**
	 * @param NodeInfo $parentNodeInfo
	 * @return void
	 */
	public function unsetChildGroupKeys(NodeInfo $parentNodeInfo);

	/**
	 * @param mixed $nestedSetEntity
	 * @param int $delta
	 * @param int $lowerBoundIncl
	 * @param int $upperBoundExcl
	 * @param array $groupKey
	 * @return void
	 */
	public function shift($nestedSetEntity, $delta, $lowerBoundIncl, $upperBoundExcl, array $groupKey);
}
