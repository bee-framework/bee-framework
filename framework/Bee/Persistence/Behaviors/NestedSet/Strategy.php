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

use Bee\Persistence\Behaviors\IOrderedStrategy;
use Bee\Persistence\Behaviors\NestedSet\IDelegate;

/**
 * User: mp
 * Date: 07.05.13
 * Time: 15:49
 */
class Strategy implements IOrderedStrategy{

	/**
	 * @var \Logger
	 */
	private static $log;

	/**
	 * @return \Logger
	 */
	protected static function getLog() {
		if (!self::$log) {
			self::$log = \Bee_Framework::getLoggerForClass(__CLASS__);
		}
		return self::$log;
	}

	/**
	 * @var IDelegate
	 */
	private $delegate;

	/**
	 * @param IDelegate $delegate
	 */
	public function __construct(IDelegate $delegate) {
		$this->delegate = $delegate;
	}

	/**
	 * @param mixed $subject
	 * @param mixed $ref
	 * @param mixed $groupRestriction
	 * @return NodeInfo
	 */
	public function moveAsFirstChild($subject, $ref, $groupRestriction = false) {
		return $this->moveRelative($subject, $ref, $groupRestriction, true, true);
	}

	/**
	 * @param mixed $subject
	 * @param mixed $ref
	 * @param mixed $groupRestriction
	 * @return NodeInfo
	 */
	public function moveAsLastChild($subject, $ref, $groupRestriction = false) {
		return $this->moveRelative($subject, $ref, $groupRestriction, false, true);
	}

	/**
	 * @param mixed $subject
	 * @param mixed $ref
	 * @param mixed $groupRestriction
	 * @return NodeInfo
	 */
	public function moveBefore($subject, $ref, $groupRestriction = false) {
		return $this->moveRelative($subject, $ref, $groupRestriction, true, false);
	}

	/**
	 * @param mixed $subject
	 * @param mixed $ref
	 * @param mixed $groupRestriction
	 * @return NodeInfo
	 */
	public function moveAfter($subject, $ref, $groupRestriction = false) {
		return $this->moveRelative($subject, $ref, $groupRestriction, false, false);
	}

	/**
	 * @param mixed $subject
	 * @param mixed $groupRestriction
	 * @return NodeInfo
	 */
	public function moveAsFirstRoot($subject, $groupRestriction = false) {
		return $this->moveRelative($subject, false, $groupRestriction, true);
	}

	/**
	 * @param mixed $subject
	 * @param mixed $groupRestriction
	 * @return NodeInfo
	 */
	public function moveAsLastRoot($subject, $groupRestriction = false) {
		return $this->moveRelative($subject, false, $groupRestriction, false);
	}

	/**
	 * @param mixed $subject
	 * @param mixed $ref
	 * @param mixed $groupRestriction
	 * @param bool $before
	 * @param bool $child
	 * @return NodeInfo
	 */
	private function moveRelative($subject, $ref, $groupRestriction = false, $before = true, $child = true) {
		// todo: handle move to new tree group

		$subjectInfo = $this->delegate->getNodeInfo($subject);

		self::getLog()->debug("retrieved subject info $subjectInfo");

		// if we do not have a ref element, we cannot append the subject as its child...
		$child = $ref ? $child : false;

		// if we have a ref, get its info, otherwise get info of tree root level
		$refInfo = $ref ?
				$this->delegate->getNodeInfo($ref, $groupRestriction) :
				$this->delegate->getTreeInfo($subject, $groupRestriction);

		self::getLog()->debug("retrieved ref info $refInfo");

		// check: do not try to move into subtree of itself (prospective parent must not be contained in subject)
		$this->checkContainment($subjectInfo, $refInfo, !$child);

		// determine new position
		if ($child) {
			$newLeft = $before ? $refInfo->lft + 1 : $refInfo->rgt;
			$level = $refInfo->lvl + 1;
		} else {
			$newLeft = $before ? $refInfo->lft : $refInfo->rgt + 1;
			$level = $refInfo->lvl;
		}

		// we don't need the ref info beyond this point
		unset($refInfo);

		// make sure the tree always starts at left position 1
		$newLeft = $newLeft ? $newLeft : 1;

		self::getLog()->debug("node should be moved to lft = $newLeft, lvl = $level");

		// todo: check if moved to new
		if ($newLeft != $subjectInfo->lft) {

			$delta = $subjectInfo->getSpan();

			// shift set boundaries
			if (!$subjectInfo->isInTree() || $newLeft < $subjectInfo->lft) {
				// shift up
				$lowerBound = $newLeft;
				$upperBound = $subjectInfo->lft >=  0 ? $subjectInfo->lft : false;
			} else {
				// shift down
				$delta *= -1;
				$lowerBound = $subjectInfo->rgt + 1;
				$upperBound = $newLeft;
				$newLeft += $delta;
			}

			// if subject previously had a valid position (i.e. is not newly inserted) ...
			if ($subjectInfo->isInTree()) {
				// ... temporarily move it to a neutral position, so as to avoid any conflicts (e.g. SQL constraints)
				// (keep its original level for now)
				$this->delegate->setPosition($subject, $subjectInfo, -$subjectInfo->getSpan(), $subjectInfo->lvl, $groupRestriction);
				$subjectInfo->update(-$subjectInfo->getSpan(), $subjectInfo->lvl);
			}

			self::getLog()->debug("shifting nodes >= $lowerBound < $upperBound by $delta");

			$this->delegate->shift($subject, $delta, $lowerBound, $upperBound, $groupRestriction);

			self::getLog()->debug("setting final position of subject to lft = $newLeft, lvl = $level");

			// move subject to final position
			$this->delegate->setPosition($subject, $subjectInfo, $newLeft, $level, $groupRestriction);
			$subjectInfo->update($newLeft, $level);
		}

		return $subjectInfo;
	}

	/**
	 * Remove the given subject and its children from the hierarchy by setting the managed set fields to NULL and
	 * restoring consistency of the rest of the hierarchy. Does not actually delete any rows!
	 * @param mixed $subject
	 * @param mixed $groupRestriction
	 * @throws \Exception
	 */
	public function remove($subject, $groupRestriction = false) {
		$subjectInfo = $this->delegate->getNodeInfo($subject);
		if(!$subjectInfo->isInTree()) {
			throw new \Exception('Trying to remove a subject that is not in a hierarchy : ' . $subject);
		}

		// store the subtree in the negative area (in case we do not want to delete it, but rather move it to a different tree)
		$this->delegate->setPosition($subject, $subjectInfo, -$subjectInfo->getSpan(), 0, $groupRestriction);

		// restore numbering consistency
		$this->delegate->shift($subject, -$subjectInfo->getSpan(), $subjectInfo->rgt + 1, false, $groupRestriction);
	}

	/**
	 * @param NodeInfo $subjectInfo
	 * @param NodeInfo $newParentInfo
	 * @param bool $strict
	 * @throws \Exception
	 */
	protected function checkContainment(NodeInfo $subjectInfo, NodeInfo $newParentInfo, $strict = true) {
		if ($subjectInfo->contains($newParentInfo, $strict)) {
			throw new \Exception('Moved node must not contain its prospective parent');
		}
	}

	/**
	 * @param mixed $subject
	 * @param mixed $groupRestriction
	 * @return NodeInfo new position
	 */
	public function moveToStart($subject, $groupRestriction = false) {
		return $this->moveAsFirstRoot($subject, $groupRestriction);
	}

	/**
	 * @param mixed $subject
	 * @param mixed $groupRestriction
	 * @return NodeInfo new position
	 */
	public function moveToEnd($subject, $groupRestriction = false) {
		return $this->moveAsLastRoot($subject, $groupRestriction);
	}
}
