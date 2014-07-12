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
use Bee\Context\Config\ArrayValue;
use Bee\Context\Config\BasicBeanDefinitionRegistry;
use Bee\Context\Config\TypedStringValue;

/**
 * User: mp
 * Date: 03.07.11
 * Time: 19:18
 */
 
class Bee_Context_Xml_BeanDefinitionReaderTest extends PHPUnit_Framework_TestCase {

    /**
     * @var BasicBeanDefinitionRegistry
     */
    private static $registry;

	/**
	 * @var PropertyEditorRegistry
	 */
	private $propertyEditorRegistry;

    public static function setUpBeforeClass() {
        self::$registry = new BasicBeanDefinitionRegistry();
        $reader = new Bee_Context_Xml_BeanDefinitionReader(self::$registry);
        $reader->loadBeanDefinitions('Bee/Context/Xml/BeanDefinitionReaderTest-context.xml');
    }

	/**
	 *
	 */
	public function setUp() {
		$this->propertyEditorRegistry = new PropertyEditorRegistry();
	}

    /**
     * @test
     */
    public function beanCount() {
        $this->assertEquals(6, self::$registry->getBeanDefinitionCount());
    }

    /**
     * @test
     */
    public function beanDefinition() {
        $this->assertTrue(self::$registry->containsBeanDefinition('arrayFactory'));

        $beanDefinition = self::$registry->getBeanDefinition('arrayFactory');

        $this->assertInstanceOf('Bee\Context\Config\IBeanDefinition', $beanDefinition);

        $this->assertEquals('Bee\Context\Util\ArrayFactoryBean', $beanDefinition->getBeanClassName());

        $propValues = $beanDefinition->getPropertyValues();

        $this->assertTrue(is_array($propValues));

        $sourceArrayProp = $propValues['sourceArray'];
        $this->assertInstanceOf('Bee\Beans\PropertyValue', $sourceArrayProp);

        $this->assertEquals('sourceArray', $sourceArrayProp->getName());
    }

    /**
     * @test
     */
    public function arrayFactoryDef() {
        $beanDefinition = self::$registry->getBeanDefinition('arrayFactory');
        $propValues = $beanDefinition->getPropertyValues();
        $sourceArrayProp = $propValues['sourceArray'];

        $arrayValue = $sourceArrayProp->getValue();

        $this->assertArrayValueInstance($arrayValue, 5);

        $this->checkParentArrayContents($arrayValue);

        $this->assertInstanceOf('Bee\Context\Config\BeanDefinitionHolder', $arrayValue[3]);
    }

    /**
     * @param ArrayValue $arrayValue
     * @return void
     */
    private function checkParentArrayContents(ArrayValue $arrayValue) {
        $this->assertTypedStringValue('Alpha', $arrayValue[0]);
        $this->assertTypedStringValue('Bravo', $arrayValue[1]);
        $this->assertTypedStringValue('Charlie', $arrayValue[2]);
        $this->assertInstanceOf('Bee\Context\Config\RuntimeBeanReference', $arrayValue[4]);
    }

	/**
	 * @test
	 */
    public function childArrayFactoryDefCheckMerge() {
		$this->childArrayFactoryDefMerged('childArrayFactoryMergedTrue');
		$this->childArrayFactoryDefMerged('childArrayFactoryMergedDefault');
		$this->childArrayFactoryDefMerged('childArrayFactoryMergedNone');
		$this->childArrayFactoryDefNotMerged('childArrayFactoryMergedFalse');
    }

	private function childArrayFactoryDefMerged($beanName) {
		$arrayValue = $this->getSourceArrayValueForArrayFactory($beanName);
		$this->assertArrayValueInstance($arrayValue, 7);
		$this->checkParentArrayContents($arrayValue);
		$this->assertInstanceOf('Bee\Context\Config\RuntimeBeanReference', $arrayValue['d']);
	}

	private function childArrayFactoryDefNotMerged($beanName) {
		$arrayValue = $this->getSourceArrayValueForArrayFactory($beanName);
		$this->assertArrayValueInstance($arrayValue, 2);
		$this->assertInstanceOf('Bee\Context\Config\RuntimeBeanReference', $arrayValue['d']);
	}

	private function getSourceArrayValueForArrayFactory($beanName) {
		$beanDefinition = self::$registry->getBeanDefinition($beanName);
		$propValues = $beanDefinition->getPropertyValues();
		$sourceArrayProp = $propValues['sourceArray'];
		$this->assertInstanceOf('Bee\Beans\PropertyValue', $sourceArrayProp);
		return $sourceArrayProp->getValue();
	}

	/**
	 * @param $stringValue
	 * @param TypedStringValue $propValue
	 */
	protected function assertTypedStringValue($stringValue, $propValue) {
        $this->assertInstanceOf('Bee\Context\Config\TypedStringValue', $propValue);
        $this->assertEquals($stringValue, $propValue->getValue($this->propertyEditorRegistry));
    }

    protected function assertArrayValueInstance($arrayValue, $expectedSize) {
        $this->assertInstanceOf('Bee\Context\Config\ArrayValue', $arrayValue);
        $this->assertEquals($expectedSize, count($arrayValue));
    }

	/**
	 * @test
	 */
	public function incomplete() {
		$this->markTestIncomplete();
	}
}