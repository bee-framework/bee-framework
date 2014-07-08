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
use Bee\Beans\PropertyEditor\PropertyEditorRegistry;
use Bee\Context\Config\TypedStringValue;

/**
 * User: mp
 * Date: 06.07.11
 * Time: 18:53
 */
class Bee_Context_AbstractTest extends PHPUnit_Framework_TestCase {

	const BEAN_NAME_ARRAY_PARENT = 'arrayParent';
	const BEAN_NAME_ARRAY_CHILD_MERGED = 'arrayChildMerged';
	const BEAN_NAME_ARRAY_CHILD_NOT_MERGED = 'arrayChildNotMerged';

	/**
	 * @var Bee_Context_Abstract
	 */
	private $context;

	protected function setUp() {
		$this->context = new Bee_Context_AbstractTestStub('Bee_Context_AbstractTest');
	}

	/**
	 * @test
	 */
	public function beanDefinitionCount() {
		$this->assertEquals(3, $this->context->getBeanDefinitionCount());
	}

	/**
	 * @test
	 */
	public function arrayMerged() {
		$beanMerged = $this->context->getBean(self::BEAN_NAME_ARRAY_CHILD_MERGED);
		$this->assertTrue(is_array($beanMerged));
		$this->assertEquals(5, count($beanMerged));
	}

	/**
	 * @test
	 */
	public function arrayNotMerged() {
		$beanMerged = $this->context->getBean(self::BEAN_NAME_ARRAY_CHILD_NOT_MERGED);
		$this->assertTrue(is_array($beanMerged));
		$this->assertEquals(2, count($beanMerged));
	}

	/**
	 * @test
	 */
	public function incomplete() {
		$this->markTestIncomplete();
	}
}

class Bee_Context_AbstractTestStub extends Bee_Context_Abstract {

	protected function loadBeanDefinitions() {
		$this->registerArrayFactoryParent();

		$this->registerBeanDefinition(Bee_Context_AbstractTest::BEAN_NAME_ARRAY_CHILD_MERGED, $this->createArrayFactoryChild(true));
		$this->registerBeanDefinition(Bee_Context_AbstractTest::BEAN_NAME_ARRAY_CHILD_NOT_MERGED, $this->createArrayFactoryChild(false));
	}

	private function registerArrayFactoryParent() {
		$builder = Bee_Context_Support_BeanDefinitionBuilder::genericBeanDefinition('Bee_Context_Util_ArrayFactoryBean');
		$propEditRegistry = new PropertyEditorRegistry();
		$list = array(
				'keyA' => new TypedStringValue('valueA', $propEditRegistry),
				'keyB' => new TypedStringValue('valueB', $propEditRegistry),
				'keyC' => new TypedStringValue('valueC', $propEditRegistry)
		);
		$builder->addPropertyValue('sourceArray', new Bee_Context_Config_ArrayValue($list, false));
		$this->registerBeanDefinition(Bee_Context_AbstractTest::BEAN_NAME_ARRAY_PARENT, $builder->getBeanDefinition());
	}

	private function createArrayFactoryChild($merge) {
		$builder = Bee_Context_Support_BeanDefinitionBuilder::genericBeanDefinition('Bee_Context_Util_ArrayFactoryBean');
		$propEditRegistry = new PropertyEditorRegistry();
		$list = array(
				'keyB' => new TypedStringValue('valueB_Child', $propEditRegistry),
				'keyD' => new TypedStringValue('valueD_Child', $propEditRegistry),
		);
		$builder->setParentName(Bee_Context_AbstractTest::BEAN_NAME_ARRAY_PARENT)
				->addPropertyValue('sourceArray', new Bee_Context_Config_ArrayValue($list, $merge));

		return $builder->getBeanDefinition();
	}
}
