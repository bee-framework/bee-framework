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
use Addendum\Annotation;

/**
 * User: mp
 * Date: 01.07.11
 * Time: 14:49
 */

class Bee_Annotations_UtilsTest /*extends PHPUnit_Framework_TestCase*/ {

    public function testFindInheritedAnnotation() {

        $class = new ReflectionClass('Bee_Annotations_UtilsTestChildAnnotatedClass');
        $method = $class->getMethod('testMethod');

        $annot = Bee_Annotations_Utils::findAnnotation($method, 'Bee_Annotations_UtilsTestAnnotation1');

//        $this->assertNotNull($annot, 'Annotation Bee_Annotations_UtilsTestAnnotation1 not found on Bee_Annotations_UtilsTestChildAnnotatedClass::testMethod()');
//
//        $this->assertEquals('parentAnnotation', $annot->value);
    }
}

class Bee_Annotations_UtilsTestAnnotation1 extends Annotation {
	public $value;
}

class Bee_Annotations_UtilsTestAnnotation2 extends Annotation {
	public $value;
}

class Bee_Annotations_UtilsTestParentAnnotatedClass {

    /**
     * @return void
     *
     * @Bee_Annotations_UtilsTestAnnotation1(value = "parentAnnotation")
     */
    public function testMethod() {
    }
}

class Bee_Annotations_UtilsTestChildAnnotatedClass extends Bee_Annotations_UtilsTestParentAnnotatedClass {

    /**
     * @return void
     *
     * @Bee_Annotations_UtilsTestAnnotation2(value = "childAnnotation")
     */
    public function testMethod() {
    }
}
