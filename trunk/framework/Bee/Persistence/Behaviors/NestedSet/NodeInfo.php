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
 * Time: 17:43
 */

class NodeInfo {

	const LEFT_KEY = 'lft';
	const RIGHT_KEY = 'rgt';
	const LEVEL_KEY = 'lvl';

	/**
	 * @var int|bool
	 */
	public $lft;

	/**
	 * @var int|bool
	 */
	public $rgt;

	/**
	 * @var int|bool
	 */
	public $lvl;

	public function __construct(array $tuple = null) {
		if(!is_null($tuple)) {
			$this->lft = is_numeric($tuple[self::LEFT_KEY]) ? $tuple[self::LEFT_KEY] : false;
			$this->rgt = is_numeric($tuple[self::RIGHT_KEY]) ? $tuple[self::RIGHT_KEY] : false;
			$this->lvl = is_numeric($tuple[self::LEVEL_KEY]) ? $tuple[self::LEVEL_KEY] : false;
		}
	}

	public function update($newLeft, $newLevel) {
		$span = $this->getSpan();
		$this->lft = $newLeft;
		$this->rgt = $newLeft + $span - 1;
		$this->lvl = $newLevel;
	}

	/**
	 * @param NodeInfo $otherPos
	 * @param bool $strict
	 * @return bool
	 */
	public function contains(NodeInfo $otherPos, $strict = true) {
		return $strict ?
				$otherPos->lft > $this->lft && $otherPos->rgt < $this->rgt :
				$otherPos->lft >= $this->lft && $otherPos->rgt <= $this->rgt;
	}

	/**
	 * @return int
	 */
	public function getSpan() {
		return $this->hasStructure() ? $this->rgt - $this->lft + 1 : 2;
	}

	/**
	 * @return float
	 */
	public function getChildCount() {
		return ($this->getSpan() / 2) - 1;
	}

	/**
	 * @return bool
	 */
	public function hasStructure() {
		return is_numeric($this->rgt) && is_numeric($this->lft);
	}

	/**
	 * @return bool
	 */
	public function isInTree() {
		return $this->rgt > 0 && $this->lft > 0;
	}

	function __toString() {
		return "NodeInfo(lft:{$this->lft}|rgt:{$this->rgt}|lvl:{$this->lvl})";
	}


}
