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
 * Time: 20:52
 */
 
class TestTreeNode extends NodeInfo implements ITreeNode {

	/**
	 * @var mixed
	 */
	private $id;

	/**
	 * @var ITreeNode
	 */
	private $parent;

	/**
	 * @var ITreeNode[]
	 */
	private $children;

	/**
	 * @param array $id
	 * @param TestTreeNode[] $children
	 * @param array $nodeInfo
	 * @return \Bee\Persistence\Behaviors\NestedSet\TestTreeNode
	 */
	public function __construct($id, $children, array $nodeInfo) {
		parent::__construct($nodeInfo);
		$this->id = $id;
		$this->children = $children;
	}

	/**
	 * @return ITreeNode[]
	 */
	public function getChildren() {
		return $this->children;
	}

	/**
	 * @return mixed
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param ITreeNode $child
	 * @return void
	 */
	public function appendChild(ITreeNode $child) {
		array_push($this->children, $child);
	}
}
