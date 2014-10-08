<?php
namespace Bee\Utils;
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
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use stdClass;

/**
 * User: mp
 * Date: 02.07.11
 * Time: 15:16
 */
 
class AssertTest extends PHPUnit_Framework_TestCase {

    /**
     * @test
     */
    public function isTrueTrue() {
        Assert::isTrue(true);
        Assert::isTrue(1);
        Assert::isTrue("foo");
        Assert::isTrue("false");
        Assert::isTrue(array("1", "2"));
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function isTrueFalseBoolean() {
		Assert::isTrue(false);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function isTrueFalseInteger() {
		Assert::isTrue(0);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function isTrueFalseEmptyArray() {
		Assert::isTrue(array());
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function isTrueFalseNull() {
		Assert::isTrue(null);
    }

    
    
    
    
    /**
     * @test
     */
    public function isNullTrue() {
		Assert::isNull(null);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function isNullFalseBooleanFalse() {
		Assert::isNull(false);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function isNullFalseBooleanTrue() {
		Assert::isNull(true);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function isNullFalseInteger() {
		Assert::isNull(0);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function isNullFalseEmptyArray() {
		Assert::isNull(array());
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function isNullFalseObject() {
		Assert::isNull(new stdClass());
    }

    /**
   	 * @test
   	 */
   	public function incomplete() {
   		$this->markTestIncomplete();
   	}
}