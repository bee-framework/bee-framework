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
use Bee\Exceptions\TypeMismatchException;
use PHPUnit_Framework_TestCase;

/**
 * User: mp
 * Date: 02.07.11
 * Time: 15:16
 */
 
class StringsTest extends PHPUnit_Framework_TestCase {

    /**
     * @test
     */
    public function hasLength() {
        $this->assertTrue(Strings::hasLength(" "));
        $this->assertTrue(Strings::hasLength("abc "));

        $this->assertFalse(Strings::hasLength(""));
        $this->assertFalse(Strings::hasLength(null));
    }

    /**
     * @test
     * @expectedException TypeMismatchException
     */
    public function hasLengthTypeMismatch() {
		Strings::hasLength(array("test")); // should throw TypeMismatchException
    }

    /**
     * @test
     */
    public function hasText() {
        $blank = "          ";
        $this->assertFalse(Strings::hasText($blank));

        $this->assertFalse(Strings::hasText(null));
        $this->assertFalse(Strings::hasText(""));

        $this->assertTrue(Strings::hasText("t"));
    }

    /**
     * @test
     * @expectedException TypeMismatchException
     */
    public function hasTextTypeMismatch() {
		Strings::hasText(array("test")); // should throw TypeMismatchException
    }

    /**
     * @test
     */
    public function tokenizeToArrayKeys() {
        $sa = Strings::tokenizeToArrayKeys("a,b , ,c", ",");
        $this->assertEquals(3, count($sa));
        $this->assertTrue($sa["a"] === true && $sa["b"] === true && $sa["c"] === true, "components are not correct");

        $sa = Strings::tokenizeToArrayKeys("a,b , ,c", ",", true, false);
        $this->assertEquals(4, count($sa));
        $this->assertTrue($sa["a"] === true && $sa["b"] === true && $sa[""] === true && $sa["c"] === true, "components are not correct");

        $sa = Strings::tokenizeToArrayKeys("a,b ,c", ",", false, true);
        $this->assertEquals(3, count($sa));
        $this->assertTrue($sa["a"] === true && $sa["b "] === true && $sa["c"] === true, "components are not correct");
    }

    /**
     * @test
     */
    public function tokenizeToArray() {
        $sa = Strings::tokenizeToArray("a,b , ,c", ",");
        $this->assertEquals(3, count($sa));
        $this->assertTrue($sa[0] === "a" && $sa[1] === "b" && $sa[2] === "c", "components are not correct");

        $sa = Strings::tokenizeToArray("a,b , ,c", ",", true, false);
        $this->assertEquals(4, count($sa));
        $this->assertTrue($sa[0] === "a" && $sa[1] === "b" && $sa[2] === "" && $sa[3] === "c", "components are not correct");

        $sa = Strings::tokenizeToArray("a,b ,c", ",", false, true);
        $this->assertEquals(3, count($sa));
        $this->assertTrue($sa[0] === "a" && $sa[1] === "b " && $sa[2] === "c", "components are not correct");
    }

    /**
     * @test
     */
    public function startsWith() {
        $this->assertTrue(Strings::startsWith(null, null));
        $this->assertTrue(Strings::startsWith("", ""));

        $this->assertFalse(Strings::startsWith("", null));
        $this->assertFalse(Strings::startsWith("abcdef", null));
        $this->assertFalse(Strings::startsWith(null, ""));
        $this->assertFalse(Strings::startsWith(null, "a"));

        $this->assertTrue(Strings::startsWith("abcdef", "a"));
        $this->assertTrue(Strings::startsWith("abcdef", "ab"));
        $this->assertTrue(Strings::startsWith("abcdef", ""));
        $this->assertTrue(Strings::startsWith("  ", ""));

        $this->assertFalse(Strings::startsWith("abcdef", "bcdef"));
        $this->assertFalse(Strings::startsWith("abcdef", " "));
   }

    /**
     * @test
     * @expectedException TypeMismatchException
     */
    public function startsWithSubjectNotString() {
		Strings::startsWith(array(), "abcd");
    }

    /**
     * @test
     * @expectedException TypeMismatchException
     */
    public function startsWithPrefixNotString() {
		Strings::startsWith("abcd", array());
    }

    /**
     * @test
     */
    public function stripPrefix() {
		$this->assertEquals(null, Strings::stripPrefix(null, ''));
		$this->assertEquals(null, Strings::stripPrefix(null, null));
        $this->assertEquals('', Strings::stripPrefix('', ''));
        $this->assertEquals('', Strings::stripPrefix('', null));

		$this->assertEquals('abcdef', Strings::stripPrefix('abcdef', ''));
		$this->assertEquals('abcdef', Strings::stripPrefix('abcdef', 'bcd'));
		$this->assertEquals('abcdef', Strings::stripPrefix('abcdef', 'def'));
		$this->assertEquals('abcdef', Strings::stripPrefix('abcdef', 'xyz'));

		$this->assertEquals('', Strings::stripPrefix('', 'xyz'));

        $this->assertEquals('def', Strings::stripPrefix('abcdef', 'abc'));
        $this->assertEquals('beeframework.org/', Strings::stripPrefix('http://beeframework.org/', 'http://'));

    }

    /**
     * @test
     */
    public function endsWith() {
		$this->assertTrue(Strings::endsWith(null, null));
		$this->assertTrue(Strings::endsWith("", ""));

		$this->assertFalse(Strings::endsWith("", null));
		$this->assertFalse(Strings::endsWith("abcdef", null));
		$this->assertFalse(Strings::endsWith(null, ""));
		$this->assertFalse(Strings::endsWith(null, "a"));

		$this->assertTrue(Strings::endsWith("abcdef", "f"));
		$this->assertTrue(Strings::endsWith("abcdef", "ef"));
		$this->assertTrue(Strings::endsWith("abcdef", ""));
		$this->assertTrue(Strings::endsWith("  ", ""));

		$this->assertFalse(Strings::endsWith("abcdef", "abcde"));
		$this->assertFalse(Strings::endsWith("abcdef", " "));
    }

	/**
	 * @test
	 * @expectedException TypeMismatchException
	 */
	public function endsWithSubjectNotString() {
		Strings::endsWith(array(), "abcd");
	}

	/**
	 * @test
	 * @expectedException TypeMismatchException
	 */
	public function endsWithSuffixNotString() {
		Strings::endsWith("abcd", array());
	}
}