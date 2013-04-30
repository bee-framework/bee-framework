<?php
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
 * Date: 03.07.11
 * Time: 21:44
 */

class Bee_Context_Config_ArrayValue implements ArrayAccess, IteratorAggregate, Countable, Bee_Context_Config_IMergeable {

	/**
	 * @var array
	 */
	private $sourceArray;

	/**
	 * @var bool
	 */
	private $mergeEnabled = false;

	private $associative = false;

	public function __construct(array &$sourceArray, $mergeEnabled = false, $associative = false) {
		$this->sourceArray =& $sourceArray;
		$this->mergeEnabled = $mergeEnabled;
		$this->associative = $associative;
	}

	/**
	 * Gets the MergeEnabled
	 *
	 * @return bool $mergeEnabled
	 */
	public function getMergeEnabled() {
		return $this->mergeEnabled;
	}

	/**
	 * Sets the MergeEnabled
	 *
	 * @param bool $mergeEnabled
	 * @return void
	 */
	public function setMergeEnabled($mergeEnabled) {
		$this->mergeEnabled = $mergeEnabled;
	}

	public function offsetExists($offset) {
		return array_key_exists($offset, $this->sourceArray);
	}

	public function offsetGet($offset) {
		return $this->sourceArray[$offset];
	}

	public function offsetSet($offset, $value) {
		$this->sourceArray[$offset] = $value;
	}

	public function offsetUnset($offset) {
		unset($this->sourceArray[$offset]);
	}

	public function count() {
		return count($this->sourceArray);
	}

	function merge(Traversable $parent) {
		$tmpArray = array();
		foreach ($parent as $key => $value) {
			if($this->associative) {
				$tmpArray[$key] = $value;
			} else {
				array_push($tmpArray, $value);
			}
		}
		foreach ($this->sourceArray as $key => $value) {
			if($this->associative) {
				$tmpArray[$key] = $value;
			} else {
				array_push($tmpArray, $value);
			}
		}
		$this->sourceArray =& $tmpArray;
	}

	public function getIterator() {
		return new ArrayIterator($this->sourceArray);
	}

	/**
	 * @return array
	 */
	public function getValue() {
		return $this->sourceArray;
	}

}
