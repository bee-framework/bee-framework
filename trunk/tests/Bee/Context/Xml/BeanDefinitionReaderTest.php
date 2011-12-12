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
 * Time: 19:18
 */
 
class Bee_Context_Xml_BeanDefinitionReaderTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Bee_Context_Config_BasicBeanDefinitionRegistry
     */
    private static $registry;

    public static function setUpBeforeClass() {
        self::$registry = new Bee_Context_Config_BasicBeanDefinitionRegistry();
        $reader = new Bee_Context_Xml_BeanDefinitionReader(self::$registry);
        $reader->loadBeanDefinitions('Bee/Context/Xml/BeanDefinitionReaderTest-context.xml');
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

        $this->assertInstanceOf('Bee_Context_Config_IBeanDefinition', $beanDefinition);

        $this->assertEquals('Bee_Context_Util_ArrayFactoryBean', $beanDefinition->getBeanClassName());

        $propValues = $beanDefinition->getPropertyValues();

        $this->assertTrue(is_array($propValues));

        $sourceArrayProp = $propValues['sourceArray'];
        $this->assertInstanceOf('Bee_Beans_PropertyValue', $sourceArrayProp);

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

        $this->assertInstanceOf('Bee_Context_Config_BeanDefinitionHolder', $arrayValue['d']);
    }

    /**
     * @param Bee_Context_Config_ArrayValue $arrayValue
     * @return void
     */
    private function checkParentArrayContents(Bee_Context_Config_ArrayValue $arrayValue) {
        $this->assertTypedStringValue('Alpha', $arrayValue[0]);
        $this->assertTypedStringValue('Bravo', $arrayValue[1]);
        $this->assertTypedStringValue('Charlie', $arrayValue[2]);
        $this->assertInstanceOf('Bee_Context_Config_RuntimeBeanReference', $arrayValue[3]);
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
		$this->assertArrayValueInstance($arrayValue, 6);
		$this->checkParentArrayContents($arrayValue);
		$this->assertInstanceOf('Bee_Context_Config_RuntimeBeanReference', $arrayValue['d']);
	}

	private function childArrayFactoryDefNotMerged($beanName) {
		$arrayValue = $this->getSourceArrayValueForArrayFactory($beanName);
		$this->assertArrayValueInstance($arrayValue, 2);
		$this->assertInstanceOf('Bee_Context_Config_RuntimeBeanReference', $arrayValue['d']);
	}

	private function getSourceArrayValueForArrayFactory($beanName) {
		$beanDefinition = self::$registry->getBeanDefinition($beanName);
		$propValues = $beanDefinition->getPropertyValues();
		$sourceArrayProp = $propValues['sourceArray'];
		$this->assertInstanceOf('Bee_Beans_PropertyValue', $sourceArrayProp);
		return $sourceArrayProp->getValue();
	}

    protected function assertTypedStringValue($stringValue, $propValue) {
        $this->assertInstanceOf('Bee_Context_Config_TypedStringValue', $propValue);
        $this->assertEquals($stringValue, $propValue->getValue());
    }

    protected function assertArrayValueInstance($arrayValue, $expectedSize) {
        $this->assertInstanceOf('Bee_Context_Config_ArrayValue', $arrayValue);
        $this->assertEquals($expectedSize, count($arrayValue));
    }

	/**
	 * @test
	 */
	public function incomplete() {
		$this->markTestIncomplete();
	}
}