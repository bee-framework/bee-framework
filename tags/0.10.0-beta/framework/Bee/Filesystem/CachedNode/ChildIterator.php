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

class Bee_Filesystem_CachedNode_ChildIterator implements Iterator, Countable {
	
	/**
	 * Enter description here...
	 *
	 * @var Bee_Filesystem_CachedNode
	 */
	private $node;

	/**
	 * Enter description here...
	 *
	 * @var array<String>
	 */
	private $nodes;
	
	/**
	 * Enter description here...
	 *
	 * @var String
	 */
	private $path;
	
	/**
	 * Enter description here...
	 *
	 * @var boolean
	 */
	private $unchanged;
	
	/**
	 * Enter description here...
	 *
	 * @var int
	 */
	private $pointer = 0;
	
	/**
	 * Enter description here...
	 *
	 * @var Bee_Filesystem_CachedNode
	 */
	private $current;
	
	/**
	 * Enter description here...
	 *
	 * @param Bee_Filesystem_Manager $manager
	 * @param array<String> $nodes
	 * @param String $path
	 * @param boolean $unchanged
	 */
	public function __construct(Bee_Filesystem_CachedNode $node, array &$nodes, $path, $unchanged = true) {
		$this->node = $node;
		$this->nodes =& $nodes;
		$this->path = $path;
		$this->unchanged = $unchanged;
		$this->setCurrentValue();
	}
	
	/**
	 * Enter description here...
	 *
	 * @return int
	 */
	public final function key() {
		return $this->pointer;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return Bee_Filesystem_CachedNode
	 */
	public final function current() {
		return $this->current;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return Bee_Filesystem_CachedNode
	 */
	public final function next() {
		$this->pointer++;
		$this->setCurrentValue();
		return $this->current;
	}
	
	/**
	 * Enter description here...
	 *
	 */
	public final function rewind() {
		if($this->pointer > 0) {
			$this->pointer = 0;
			$this->setCurrentValue();
		}
	}
	
	/**
	 * Enter description here...
	 *
	 * @return boolean
	 */
	public final function valid() {
		 return $this->current;
	}
	
	public final function count() {
		return count($this->nodes);
	}
	
	private function setCurrentValue() {
		$this->current = count($this->nodes) > $this->pointer ? new Bee_Filesystem_CachedNode($this->node->getManager(), $this->path . DIRECTORY_SEPARATOR . $this->nodes[$this->pointer], $this->unchanged) : false;
	}
}
?>