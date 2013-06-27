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
 * Time: 20:48
 */

class TreeStrategyTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @test
	 * @dataProvider nodeInfoTestDataProvider
	 */
	public function testCalculateNodeInfo(IDelegate $delegate, $root, $expectedResult) {
		$cut = new TreeStrategyMock($delegate);
		$cut->calculateNodeInfo($root, $root->lft, $root->lvl);

		$stack = array($root);

		while (count($stack) > 0) {
			$current = array_pop($stack);
			$this->assertEquals($current->__toString(), $cut->getNodeInfoCache()->offsetGet($current)->__toString());
			$stack = array_merge($stack, $current->getChildren());
		}
	}

	/**
	 * @test
	 * @dataProvider nodeInfoTestDataProvider
	 */
	public function testSaveStructure(IDelegate $delegate, $root, $expectedResult) {
		$cut = new TreeStrategyMock($delegate);
		$cut->saveStructure($root);
		$this->assertEquals($expectedResult, implode('|', $delegate->protocol));
	}

	public function nodeInfoTestDataProvider() {
		return array(
			array(
				new DelegateMock(1, 2, 0),
				new TestTreeNode('A',
					array(
						new TestTreeNode('B',
							array(
								new TestTreeNode('E',
									array(),
									array(
										NodeInfo::LEFT_KEY => 3,
										NodeInfo::LEVEL_KEY => 2,
										NodeInfo::RIGHT_KEY => 4
									)
								),
								new TestTreeNode('F',
									array(),
									array(
										NodeInfo::LEFT_KEY => 5,
										NodeInfo::LEVEL_KEY => 2,
										NodeInfo::RIGHT_KEY => 6
									)
								)
							),
							array(
								NodeInfo::LEFT_KEY => 2,
								NodeInfo::LEVEL_KEY => 1,
								NodeInfo::RIGHT_KEY => 7
							)
						),
						new TestTreeNode('C',
							array(),
							array(
								NodeInfo::LEFT_KEY => 8,
								NodeInfo::LEVEL_KEY => 1,
								NodeInfo::RIGHT_KEY => 9
							)
						),
						new TestTreeNode('D',
							array(),
							array(
								NodeInfo::LEFT_KEY => 10,
								NodeInfo::LEVEL_KEY => 1,
								NodeInfo::RIGHT_KEY => 11
							)
						)
					),
					array(
						NodeInfo::LEFT_KEY => 1,
						NodeInfo::LEVEL_KEY => 0,
						NodeInfo::RIGHT_KEY => 12
					)
				),
				'delta=10; 3<=lft/rgt;|id=A; lft=1; rgt=12; lvl=0;|id=D; lft=10; rgt=11; lvl=1;|id=C; lft=8; rgt=9; lvl=1;|id=B; lft=2; rgt=7; lvl=1;|id=F; lft=5; rgt=6; lvl=2;|id=E; lft=3; rgt=4; lvl=2;'
			)
		);
	}
}

class TreeStrategyMock extends TreeStrategy {
	public function calculateNodeInfo(ITreeNode $currentNode, $lft, $lvl) {
		return parent::calculateNodeInfo($currentNode, $lft, $lvl);
	}

	public function getNodeInfoCache() {
		return parent::getNodeInfoCache();
	}
}
