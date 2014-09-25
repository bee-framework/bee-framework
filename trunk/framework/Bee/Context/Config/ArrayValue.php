<?php
namespace Bee\Context\Config;
/*
 * Copyright 2008-2014 the original author or authors.
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
use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * User: mp
 * Date: 03.07.11
 * Time: 21:44
 */

class ArrayValue implements ArrayAccess, IteratorAggregate, Countable, IMergeable {

	/**
	 * @var array
	 */
	private $sourceArray;

	/**
	 * @var bool
	 */
	private $mergeEnabled = false;

	/**
	 * @var bool
	 */
	private $associative = false;

	/**
	 * @var bool
	 */
	private $numericKeys = false;

	/**
	 * @param array $sourceArray
	 * @param bool $mergeEnabled
	 * @param bool $associative
	 * @param bool $numericKeys
	 */
	public function __construct(array &$sourceArray, $mergeEnabled = false, $associative = false, $numericKeys = false) {
		$this->sourceArray =& $sourceArray;
		$this->mergeEnabled = $mergeEnabled;
		$this->associative = $associative;
		$this->numericKeys = $numericKeys;
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

	/**
	 * @param mixed $offset
	 * @return mixed
	 */
	public function offsetGet($offset) {
		return $this->sourceArray[$offset];
	}

	/**
	 * @param mixed $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value) {
		$this->sourceArray[$offset] = $value;
	}

	/**
	 * @param mixed $offset
	 */
	public function offsetUnset($offset) {
		unset($this->sourceArray[$offset]);
	}

	/**
	 * @return int
	 */
	public function count() {
		return count($this->sourceArray);
	}

	/**
	 * @param Traversable $parent
	 */
	function merge(Traversable $parent) {
		$tmpArray = array();
		$parentAssoc = $parent instanceof ArrayValue && $parent->associative;
		if($parentAssoc && $this->associative && $parent->numericKeys && $this->numericKeys) {
			$tmpArray = array_replace($parent->sourceArray, $this->sourceArray);
			ksort($tmpArray);
		} else {
			foreach ($parent as $key => $value) {
				if($parentAssoc) {
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
		}
		$this->sourceArray =& $tmpArray;
	}

	/**
	 * @return ArrayIterator|Traversable
	 */
	public function getIterator() {
		return new ArrayIterator($this->sourceArray);
	}

	/**
	 * @return array
	 */
	public function getValue() {
		return $this->sourceArray;
	}

	/*
	 *
	 */
	public function setValue(array $value) {
		$this->sourceArray = $value;
	}
}
