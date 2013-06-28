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
		$this->delegate->unsetChildGroupKeys($rootNodeInfo);
		$oldNext = $rootNodeInfo->rgt + 1;

		$next = $this->calculateNodeInfo($structureRoot, $rootNodeInfo->lft, $rootNodeInfo->lvl, $rootNodeInfo->getGroupKey());

		$delta = $next - $oldNext;
		$this->delegate->shift($structureRoot, $delta, $oldNext, false, $rootNodeInfo->getGroupKey());

		$myDelegate = $this->delegate;
		$this->walkTree($structureRoot, function(ITreeNode $currentNode, NodeInfo $nodeInfo) use ($myDelegate) {
			$myDelegate->setPosition($currentNode, $nodeInfo);
		});
	}

	/**
	 * Walk the tree under $structureRoot iteratively in preorder and apply the given lambda $func to each node.
	 * @param ITreeNode $structureRoot
	 * @param $func
	 */
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
	 * @param array $groupKey
	 * @return mixed
	 */
	protected function calculateNodeInfo(ITreeNode $currentNode, $lft, $lvl, array $groupKey) {
		$nodeInfo = new NodeInfo();
		$this->nodeInfoCache->attach($currentNode, $nodeInfo);

		$nodeInfo->lvl = $lvl;
		$nodeInfo->lft = $lft;
		$lft++;
		foreach($currentNode->getChildren() as $child) {
			$lft = $this->calculateNodeInfo($child, $lft, $lvl + 1, $groupKey);
		}
		$nodeInfo->rgt = $lft;
		$nodeInfo->setGroupKey($groupKey);

		return $nodeInfo->rgt + 1;
	}

	/**
	 * @return \SplObjectStorage
	 */
	protected function getNodeInfoCache() {
		return $this->nodeInfoCache;
	}

	/**
	 * @param ITreeNode[] $nodesList
	 * @return ITreeNode
	 */
	public function buildTreeStructure(array $nodesList) {
		$nodeStack = new \SplStack();

		$lastNode = null;
		$lastLevel = false;

		foreach ($nodesList as $node) {
			$level = $this->delegate->getNodeInfo($node)->lvl;

			if ($lastLevel !== false) {
				if ($level > $lastLevel) {
					// dive exactly one level
					// must be exactly 1 larger than last level (otherwise intermediate nodes are probably missing)
					\Bee_Utils_Assert::isTrue($level == $lastLevel + 1, sprintf('Malformed nodes list, missing intermediate levels between %d and %d', $lastLevel, $level));
					// use last node as current parent
					$nodeStack->push($lastNode);
				} else {
					// $lastLevel >= $level; emerge one or multiple levels
					for ($i = $lastLevel; $i > $level; $i--) {
						$nodeStack->pop();
					}
				}

				// add to current parent (which must exist!!)
				\Bee_Utils_Assert::isTrue(!$nodeStack->isEmpty(), sprintf('No current parent on level %d (if this happens on level 0, it usually means that your query returned multiple roots for this tree set)', $level));

				$nodeStack->top()->appendChild($node);
			}

			$lastLevel = $level;
			$lastNode = $node;
		}

		return $nodeStack->bottom();
	}


}
