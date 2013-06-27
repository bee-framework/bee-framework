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
 * Time: 18:19
 */
 
class TreeStrategy {

	/**
	 * @var \SplObjectStorage
	 */
	private $nodeInfoCache;

	/**
	 * @var IDelegate
	 */
	private $delegate;

	/**
	 * @param IDelegate $delegate
	 */
	public function __construct(IDelegate $delegate) {
		$this->delegate = $delegate;
		$this->nodeInfoCache = new \SplObjectStorage();
	}

	public function saveStructure(ITreeNode $structureRoot) {
		$rootNodeInfo = $this->nodeInfoCache->contains($structureRoot) ? $this->nodeInfoCache->offsetGet($structureRoot) : $this->delegate->getNodeInfo($structureRoot);
		$oldNext = $rootNodeInfo->rgt + 1;

		$next = $this->calculateNodeInfo($structureRoot, $rootNodeInfo->lft, $rootNodeInfo->lvl);

		$delta = $next - $oldNext;
		$this->delegate->shift($structureRoot, $delta, $oldNext, false);

		$myDelegate = $this->delegate;
		$this->walkTree($structureRoot, function(ITreeNode $currentNode, NodeInfo $nodeInfo) use ($myDelegate) {
			$myDelegate->setPosition($currentNode, $nodeInfo);
		});
	}

	protected function walkTree(ITreeNode $structureRoot, $func) {
		$stack = array($structureRoot);
		while(count($stack) > 0) {
			$current = array_pop($stack);
			$func($current, $this->getNodeInfoCache()->offsetGet($current));
			$stack = array_merge($stack, $current->getChildren());
		}
	}

	/**
	 *
	 * todo: create an iterative implementation of this method
	 * @param ITreeNode $currentNode
	 * @param $lft
	 * @param $lvl
	 * @return mixed
	 */
	protected function calculateNodeInfo(ITreeNode $currentNode, $lft, $lvl) {
		$nodeInfo = new NodeInfo();
		$this->nodeInfoCache->attach($currentNode, $nodeInfo);

		$nodeInfo->lvl = $lvl;
		$nodeInfo->lft = $lft;
		$lft++;
		foreach($currentNode->getChildren() as $child) {
			$lft = $this->calculateNodeInfo($child, $lft, $lvl + 1);
		}
		$nodeInfo->rgt = $lft;

		return $nodeInfo->rgt + 1;
	}

	/**
	 * @return \SplObjectStorage
	 */
	protected function getNodeInfoCache() {
		return $this->nodeInfoCache;
	}
}
