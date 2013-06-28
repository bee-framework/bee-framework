<?php
namespace Treetest;

use Bee\Persistence\Behaviors\NestedSet\ITreeNode;
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
 * Date: 21.06.13
 * Time: 14:20
 *
 * @Entity
 * @Table(
 * 		name="tree_test"
 * )
 */
class Node implements ITreeNode {

	/**
	 * @var int
	 * @Id
	 * @Column(type="integer")
	 * @GeneratedValue
	 */
	private $id;

	/**
	 * @var string
	 * @Column(type="string", nullable=false)
	 */
	private $name;

	/**
	 * @var int
	 * @Column(name="root_id", type="integer", nullable=true)
	 */
	private $rootId;

	/**
	 * @var int
	 * @Column(type="integer", nullable=true)
	 */
	private $lft;

	/**
	 * @var int
	 * @Column(type="integer", nullable=true)
	 */
	private $rgt;

	/**
	 * @var int
	 * @Column(type="integer", nullable=true)
	 */
	private $lvl;

	/**
	 * @var Node
	 */
	private $parent;

	/**
	 * @var Node[]
	 */
	private $children = array();

	/**
	 * @param $name string
	 * @param null $rootId
	 */
	public function __construct($name, $rootId = null) {
		$this->name = $name;
		$this->rootId = $rootId;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param int $lft
	 */
	public function setLft($lft) {
		$this->lft = $lft;
	}

	/**
	 * @return int
	 */
	public function getLft() {
		return $this->lft;
	}

	/**
	 * @param int $lvl
	 */
	public function setLvl($lvl) {
		$this->lvl = $lvl;
	}

	/**
	 * @return int
	 */
	public function getLvl() {
		return $this->lvl;
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param int $rgt
	 */
	public function setRgt($rgt) {
		$this->rgt = $rgt;
	}

	/**
	 * @return int
	 */
	public function getRgt() {
		return $this->rgt;
	}

	/**
	 * @param int $rootId
	 */
	public function setRootId($rootId) {
		$this->rootId = $rootId;
	}

	/**
	 * @return int
	 */
	public function getRootId() {
		return $this->rootId;
	}

	/**
	 * @return ITreeNode[]
	 */
	public function getChildren() {
		return $this->children;
	}

	/**
	 * @param ITreeNode $child
	 * @return void
	 */
	public function appendChild(ITreeNode $child) {
		array_push($this->children, $child);
	}
}
