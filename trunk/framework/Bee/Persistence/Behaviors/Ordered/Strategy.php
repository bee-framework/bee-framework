<?php
namespace Bee\Persistence\Behaviors\Ordered;

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
 * Date: 05.05.13
 * Time: 23:08
 */
class Strategy {

	/**
	 * @var IDelegate
	 */
	private $delegate;

	public function __construct(IDelegate $delgate) {
		$this->delegate = $delgate;
	}

	/**
	 * @param mixed $subject
	 * @param mixed $ref
	 * @param mixed $groupRestriction
	 * @return int
	 */
	public function moveBefore($subject, $ref, $groupRestriction = false) {
		return $this->moveRelative($subject, $ref, true, $groupRestriction);
	}

	/**
	 * @param mixed $subject
	 * @param mixed $ref
	 * @param mixed $groupRestriction
	 * @return int
	 */
	public function moveAfter($subject, $ref, $groupRestriction = false) {
		return $this->moveRelative($subject, $ref, false, $groupRestriction);
	}

	/**
	 * @param mixed $subject
	 * @param mixed $groupRestriction
	 * @return int
	 */
	public function moveToStart($subject, $groupRestriction = false) {
		return $this->moveAfter($subject, false, $groupRestriction);
	}

	/**
	 * @param mixed $subject
	 * @param mixed $groupRestriction
	 * @return int
	 */
	public function moveToEnd($subject, $groupRestriction = false) {
		return $this->moveBefore($subject, false, $groupRestriction);
	}

	/**
	 * @param mixed $subject
	 * @param mixed $ref
	 * @param bool $before
	 * @param mixed $groupRestriction
	 * @return int
	 */
	public function moveRelative($subject, $ref, $before = true, $groupRestriction = false) {
		// determine previous position of subject
		$oldPos = $this->delegate->getPosition($subject, $groupRestriction);

		// is there a reference element given?
		if ($ref) {
			// yes, drop on the position following the referenced element (or on its position proper, in case the moved
			// element preceded the reference element in the list
			$newPos = $this->delegate->getPosition($ref, $groupRestriction);

			if (!$before && $oldPos > $newPos) {
				$newPos += 1;
			} else if ($before && $oldPos < $newPos) {
				$newPos -= 1;
			}
		} else {
			// no, "move behind nothing" means "move to beginning", "move before nothing" means "move to the end"
			$newPos = $before ? $this->delegate->getMaxPosition($subject, $groupRestriction) + 1 : 0;
		}

		// actual move?
		if ($oldPos != $newPos) {
			// temporarily move the subject to a neutral position, so as to avoid any conflicts (e.g. SQL constraints)
			$this->delegate->setPosition($subject, -1, $groupRestriction);
			// shift remaining entries (possibly including reference element)
			$this->delegate->shiftPosition($subject, $newPos, $oldPos, $groupRestriction);
			// set final position on subject
			$this->delegate->setPosition($subject, $newPos, $groupRestriction);
		}
		// that's all, folks!

		return $newPos;
	}
}
