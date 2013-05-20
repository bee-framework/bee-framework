<?php
namespace Bee\Persistence\Behaviors;
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
 * Date: 20.05.13
 * Time: 00:33
 */

interface IOrderedStrategy {
	/**
	 * @param mixed $subject
	 * @param mixed $ref
	 * @param mixed $groupRestriction
	 * @return mixed new position
	 */
	public function moveBefore($subject, $ref, $groupRestriction = false);

	/**
	 * @param mixed $subject
	 * @param mixed $ref
	 * @param mixed $groupRestriction
	 * @return mixed new position
	 */
	public function moveAfter($subject, $ref, $groupRestriction = false);

	/**
	 * @param mixed $subject
	 * @param mixed $groupRestriction
	 * @return mixed new position
	 */
	public function moveToStart($subject, $groupRestriction = false);

	/**
	 * @param mixed $subject
	 * @param mixed $groupRestriction
	 * @return mixed new position
	 */
	public function moveToEnd($subject, $groupRestriction = false);

	/**
	 * @param mixed $subject
	 * @param mixed $groupRestriction
	 */
	public function remove($subject, $groupRestriction = false);
}
