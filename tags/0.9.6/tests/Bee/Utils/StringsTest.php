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
 
class Bee_Utils_StringsTest extends PHPUnit_Framework_TestCase {

    /**
     * @test
     */
    public function hasLength() {
        $this->assertTrue(Bee_Utils_Strings::hasLength(" "));
        $this->assertTrue(Bee_Utils_Strings::hasLength("abc "));

        $this->assertFalse(Bee_Utils_Strings::hasLength(""));
        $this->assertFalse(Bee_Utils_Strings::hasLength(null));
    }

    /**
     * @test
     * @expectedException Bee_Exceptions_TypeMismatch
     */
    public function hasLengthTypeMismatch() {
        Bee_Utils_Strings::hasLength(array("test")); // should throw Bee_Exceptions_TypeMismatch
    }

    /**
     * @test
     */
    public function hasText() {
        $blank = "          ";
        $this->assertFalse(Bee_Utils_Strings::hasText($blank));

        $this->assertFalse(Bee_Utils_Strings::hasText(null));
        $this->assertFalse(Bee_Utils_Strings::hasText(""));

        $this->assertTrue(Bee_Utils_Strings::hasText("t"));
    }

    /**
     * @test
     * @expectedException Bee_Exceptions_TypeMismatch
     */
    public function hasTextTypeMismatch() {
        Bee_Utils_Strings::hasText(array("test")); // should throw Bee_Exceptions_TypeMismatch
    }

    /**
     * @test
     */
    public function tokenizeToArrayKeys() {
        $sa = Bee_Utils_Strings::tokenizeToArrayKeys("a,b , ,c", ",");
        $this->assertEquals(3, count($sa));
        $this->assertTrue($sa["a"] === true && $sa["b"] === true && $sa["c"] === true, "components are not correct");

        $sa = Bee_Utils_Strings::tokenizeToArrayKeys("a,b , ,c", ",", true, false);
        $this->assertEquals(4, count($sa));
        $this->assertTrue($sa["a"] === true && $sa["b"] === true && $sa[""] === true && $sa["c"] === true, "components are not correct");

        $sa = Bee_Utils_Strings::tokenizeToArrayKeys("a,b ,c", ",", false, true);
        $this->assertEquals(3, count($sa));
        $this->assertTrue($sa["a"] === true && $sa["b "] === true && $sa["c"] === true, "components are not correct");
    }

    /**
     * @test
     */
    public function tokenizeToArray() {
        $sa = Bee_Utils_Strings::tokenizeToArray("a,b , ,c", ",");
        $this->assertEquals(3, count($sa));
        $this->assertTrue($sa[0] === "a" && $sa[1] === "b" && $sa[2] === "c", "components are not correct");

        $sa = Bee_Utils_Strings::tokenizeToArray("a,b , ,c", ",", true, false);
        $this->assertEquals(4, count($sa));
        $this->assertTrue($sa[0] === "a" && $sa[1] === "b" && $sa[2] === "" && $sa[3] === "c", "components are not correct");

        $sa = Bee_Utils_Strings::tokenizeToArray("a,b ,c", ",", false, true);
        $this->assertEquals(3, count($sa));
        $this->assertTrue($sa[0] === "a" && $sa[1] === "b " && $sa[2] === "c", "components are not correct");
    }

    /**
     * @test
     */
    public function startsWith() {
        $this->assertTrue(Bee_Utils_Strings::startsWith(null, null));
        $this->assertTrue(Bee_Utils_Strings::startsWith("", ""));

        $this->assertFalse(Bee_Utils_Strings::startsWith("", null));
        $this->assertFalse(Bee_Utils_Strings::startsWith("abcdef", null));
        $this->assertFalse(Bee_Utils_Strings::startsWith(null, ""));
        $this->assertFalse(Bee_Utils_Strings::startsWith(null, "a"));

        $this->assertTrue(Bee_Utils_Strings::startsWith("abcdef", "a"));
        $this->assertTrue(Bee_Utils_Strings::startsWith("abcdef", "ab"));
        $this->assertTrue(Bee_Utils_Strings::startsWith("abcdef", ""));
        $this->assertTrue(Bee_Utils_Strings::startsWith("  ", ""));

        $this->assertFalse(Bee_Utils_Strings::startsWith("abcdef", "bcdef"));
        $this->assertFalse(Bee_Utils_Strings::startsWith("abcdef", " "));
   }

    /**
     * @test
     * @expectedException Bee_Exceptions_TypeMismatch
     */
    public function startsWithSubjectNotString() {
        Bee_Utils_Strings::startsWith(array(), "abcd");
    }

    /**
     * @test
     * @expectedException Bee_Exceptions_TypeMismatch
     */
    public function startsWithPrefixNotString() {
        Bee_Utils_Strings::startsWith("abcd", array());
    }

    /**
     * @test
     */
    public function stripPrefix() {
		$this->assertEquals(null, Bee_Utils_Strings::stripPrefix(null, ''));
		$this->assertEquals(null, Bee_Utils_Strings::stripPrefix(null, null));
        $this->assertEquals('', Bee_Utils_Strings::stripPrefix('', ''));
        $this->assertEquals('', Bee_Utils_Strings::stripPrefix('', null));

		$this->assertEquals('abcdef', Bee_Utils_Strings::stripPrefix('abcdef', ''));
		$this->assertEquals('abcdef', Bee_Utils_Strings::stripPrefix('abcdef', 'bcd'));
		$this->assertEquals('abcdef', Bee_Utils_Strings::stripPrefix('abcdef', 'def'));
		$this->assertEquals('abcdef', Bee_Utils_Strings::stripPrefix('abcdef', 'xyz'));

		$this->assertEquals('', Bee_Utils_Strings::stripPrefix('', 'xyz'));

        $this->assertEquals('def', Bee_Utils_Strings::stripPrefix('abcdef', 'abc'));
        $this->assertEquals('beeframework.org/', Bee_Utils_Strings::stripPrefix('http://beeframework.org/', 'http://'));

    }

    /**
     * @test
     */
    public function endsWith() {
		$this->assertTrue(Bee_Utils_Strings::endsWith(null, null));
		$this->assertTrue(Bee_Utils_Strings::endsWith("", ""));

		$this->assertFalse(Bee_Utils_Strings::endsWith("", null));
		$this->assertFalse(Bee_Utils_Strings::endsWith("abcdef", null));
		$this->assertFalse(Bee_Utils_Strings::endsWith(null, ""));
		$this->assertFalse(Bee_Utils_Strings::endsWith(null, "a"));

		$this->assertTrue(Bee_Utils_Strings::endsWith("abcdef", "f"));
		$this->assertTrue(Bee_Utils_Strings::endsWith("abcdef", "ef"));
		$this->assertTrue(Bee_Utils_Strings::endsWith("abcdef", ""));
		$this->assertTrue(Bee_Utils_Strings::endsWith("  ", ""));

		$this->assertFalse(Bee_Utils_Strings::endsWith("abcdef", "abcde"));
		$this->assertFalse(Bee_Utils_Strings::endsWith("abcdef", " "));
    }

	/**
	 * @test
	 * @expectedException Bee_Exceptions_TypeMismatch
	 */
	public function endsWithSubjectNotString() {
		Bee_Utils_Strings::endsWith(array(), "abcd");
	}

	/**
	 * @test
	 * @expectedException Bee_Exceptions_TypeMismatch
	 */
	public function endsWithSuffixNotString() {
		Bee_Utils_Strings::endsWith("abcd", array());
	}

}