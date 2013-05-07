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
 * Time: 23:07
 */
interface IDelegate {

	/**
	 * @param mixed $orderedEntity
	 * @param mixed $restriction
	 * @return int
	 */
	public function getPosition($orderedEntity, $restriction = false);

	/**
	 * @param mixed $orderedEntity
	 * @param mixed $restriction
	 * @return int
	 */
	public function getMaxPosition($orderedEntity, $restriction = false);

	/**
	 * @param mixed $orderedEntity
	 * @param int $newPos
	 * @param int $oldPos
	 * @param $restriction
	 */
	public function shiftPosition($orderedEntity, $newPos, $oldPos, $restriction = false);

	/**
	 * @param mixed $orderedEntity
	 * @param int $newPos
	 * @param mixed $restriction
	 */
	public function setPosition($orderedEntity, $newPos, $restriction = false);
}
