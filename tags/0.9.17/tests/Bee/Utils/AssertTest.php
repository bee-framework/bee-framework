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
 * Date: 02.07.11
 * Time: 15:16
 */
 
class Bee_Utils_AssertTest extends PHPUnit_Framework_TestCase {

    /**
     * @test
     */
    public function isTrueTrue() {
        Bee_Utils_Assert::isTrue(true);
        Bee_Utils_Assert::isTrue(1);
        Bee_Utils_Assert::isTrue("foo");
        Bee_Utils_Assert::isTrue("false");
        Bee_Utils_Assert::isTrue(array("1", "2"));
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function isTrueFalseBoolean() {
        Bee_Utils_Assert::isTrue(false);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function isTrueFalseInteger() {
        Bee_Utils_Assert::isTrue(0);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function isTrueFalseEmptyArray() {
        Bee_Utils_Assert::isTrue(array());
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function isTrueFalseNull() {
        Bee_Utils_Assert::isTrue(null);
    }

    
    
    
    
    /**
     * @test
     */
    public function isNullTrue() {
        Bee_Utils_Assert::isNull(null);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function isNullFalseBooleanFalse() {
        Bee_Utils_Assert::isNull(false);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function isNullFalseBooleanTrue() {
        Bee_Utils_Assert::isNull(true);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function isNullFalseInteger() {
        Bee_Utils_Assert::isNull(0);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function isNullFalseEmptyArray() {
        Bee_Utils_Assert::isNull(array());
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function isNullFalseObject() {
        Bee_Utils_Assert::isNull(new stdClass());
    }

    /**
   	 * @test
   	 */
   	public function incomplete() {
   		$this->markTestIncomplete();
   	}
}