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
use PHPUnit_Framework_TestCase;

/**
 * Class AntPathDataProvider
 */
abstract class AntPathDataProvider extends PHPUnit_Framework_TestCase {

	public function matchDataProvider() {
		return array(
			// test exact matching
				array("test", "test", True), // #0
				array("/test", "/test", True), // #1
				array("/test.jpg", "test.jpg", False), // #2
				array("test", "/test", False), // #3
				array("/test", "test", False), // #4

			// test matching with ?'s
				array("t?st", "test", True), // #5
				array("??st", "test", True), // #6
				array("tes?", "test", True), // #7
				array("te??", "test", True), // #8
				array("?es?", "test", True), // #9
				array("tes?", "tes", False), // #10
				array("tes?", "testt", False),
				array("tes?", "tsst", False),

			// test matchin with *'s
				array("*", "test", True),
				array("test*", "test", True),
				array("test*", "testTest", True), // #15
				array("test/*", "test/Test", True),
				array("test/*", "test/t", True),
				array("test/*", "test/", True),
				array("*test*", "AnothertestTest", True),
				array("*test", "Anothertest", True), // #20
				array("*.*", "test.", True),
				array("*.*", "test.test", True),
				array("*.*", "test.test.test", True),
				array("test*aaa", "testblaaaa", True),
				array("test*", "tst", False), // #25
				array("test*", "tsttest", False),
				array("test*", "test/", False),
				array("test*", "test/t", False),
				array("test/*", "test", False),
				array("*test*", "tsttst", False), // #30
				array("*test", "tsttst", False),
				array("*.*", "tsttst", False),
				array("test*aaa", "test", False),
				array("test*aaa", "testblaaab", False),

			// test matching with ?'s and /'s
				array("/?", "/a", True), // #35
				array("/?/a", "/a/a", True),
				array("/a/?", "/a/b", True),
				array("/??/a", "/aa/a", True),
				array("/a/??", "/a/bb", True),
				array("/?", "/a", True), // #40

			// test matching with **'s
				array("/**", "/testing/testing", True),						// #41
				array("/*/**", "/testing/testing", True),					// #42
				array("/**/*", "/testing/testing", True),					// #43
				array("/bla/**/bla", "/bla/testing/testing/bla", True),		// #44
				array("/bla/**/bla", "/bla/testing/testing/bla/bla", True), // #45
				array("/**/test", "/bla/bla/test", True),					// #48
				array("/**/test", "/test", True),							// #47
				array("/bla/**/**/bla", "/bla/bla/bla/bla/bla/bla", True),	// #48
				array("/bla*bla/test", "/blaXXXbla/test", True),			// #49
				array("/*bla/test", "/XXXbla/test", True),					// #50
				array("/bla*bla/test", "/blaXXXbl/test", False),			// #51
				array("/*bla/test", "XXXblab/test", False),					// #52
				array("/*bla/test", "XXXbl/test", False),					// #53
				array("/*bla/test", "/bla/test", true),						// #54

				array("/????", "/bala/bla", False),
				array("/**/*bla", "/bla/bla/bla/bbb", False),

				array("/*bla*/**/bla/**", "/XXXblaXXXX/testing/testing/bla/testing/testing/", True), // #55
				array("/*bla*/**/bla/*", "/XXXblaXXXX/testing/testing/bla/testing", True),
				array("/*bla*/**/bla/**", "/XXXblaXXXX/testing/testing/bla/testing/testing", True),
				array("/*bla*/**/bla/**", "/XXXblaXXXX/testing/testing/bla/testing/testing.jpg", True),

				array("*bla*/**/bla/**", "XXXblaXXXX/testing/testing/bla/testing/testing/", True),
				array("*bla*/**/bla/*", "XXXblaXXXX/testing/testing/bla/testing", True), // #60
				array("*bla*/**/bla/**", "XXXblaXXXX/testing/testing/bla/testing/testing", True),
				array("*bla*/**/bla/*", "XXXblaXXXX/testing/testing/bla/testing/testing", False),

				array("/x/x/**/bla", "/x/x/x/", False),

				array("", "", True)//,

//            array("/{bla}.*", "/testing.html", True)                                                // #65
		);
	}

	public function withMatchStartDataProvider() {
		return array(
			// test exact matching
				array("test", "test", True),
				array("/test", "/test", True),
				array("/test.jpg", "test.jpg", False),
				array("test", "/test", False),
				array("/test", "test", False),

			// test matching with ?'s
				array("t?st", "test", True),
				array("??st", "test", True),
				array("tes?", "test", True),
				array("te??", "test", True),
				array("?es?", "test", True),
				array("tes?", "tes", False),
				array("tes?", "testt", False),
				array("tes?", "tsst", False),

			// test matchin with *'s
				array("*", "test", True),
				array("test*", "test", True),
				array("test*", "testTest", True),
				array("test/*", "test/Test", True),
				array("test/*", "test/t", True),
				array("test/*", "test/", True),
				array("*test*", "AnothertestTest", True),
				array("*test", "Anothertest", True),
				array("*.*", "test.", True),
				array("*.*", "test.test", True),
				array("*.*", "test.test.test", True),
				array("test*aaa", "testblaaaa", True),
				array("test*", "tst", False),
				array("test*", "test/", False),
				array("test*", "tsttest", False),
				array("test*", "test/", False),
				array("test*", "test/t", False),
				array("test/*", "test", True),
				array("test/t*.txt", "test", True),
				array("*test*", "tsttst", False),
				array("*test", "tsttst", False),
				array("*.*", "tsttst", False),
				array("test*aaa", "test", False),
				array("test*aaa", "testblaaab", False),

			// test matching with ?'s and /'s
				array("/?", "/a", True),
				array("/?/a", "/a/a", True),
				array("/a/?", "/a/b", True),
				array("/??/a", "/aa/a", True),
				array("/a/??", "/a/bb", True),
				array("/?", "/a", True),

			// test matching with **'s
				array("/**", "/testing/testing", True),
				array("/*/**", "/testing/testing", True),
				array("/**/*", "/testing/testing", True),
				array("test*/**", "test/", True),
				array("test*/**", "test/t", True),
				array("/bla/**/bla", "/bla/testing/testing/bla", True),
				array("/bla/**/bla", "/bla/testing/testing/bla/bla", True),
				array("/**/test", "/bla/bla/test", True),
				array("/**/test", "/test", True),
				array("/bla/**/**/bla", "/bla/bla/bla/bla/bla/bla", True),
				array("/bla*bla/test", "/blaXXXbla/test", True),
				array("/*bla/test", "/XXXbla/test", True),
				array("/bla*bla/test", "/blaXXXbl/test", False),
				array("/*bla/test", "XXXblab/test", False),
				array("/*bla/test", "XXXbl/test", False),

				array("/????", "/bala/bla", False),
				array("/**/*bla", "/bla/bla/bla/bbb", True),

				array("/*bla*/**/bla/**", "/XXXblaXXXX/testing/testing/bla/testing/testing/", True),
				array("/*bla*/**/bla/*", "/XXXblaXXXX/testing/testing/bla/testing", True),
				array("/*bla*/**/bla/**", "/XXXblaXXXX/testing/testing/bla/testing/testing", True),
				array("/*bla*/**/bla/**", "/XXXblaXXXX/testing/testing/bla/testing/testing.jpg", True),

				array("*bla*/**/bla/**", "XXXblaXXXX/testing/testing/bla/testing/testing/", True),
				array("*bla*/**/bla/*", "XXXblaXXXX/testing/testing/bla/testing", True),
				array("*bla*/**/bla/**", "XXXblaXXXX/testing/testing/bla/testing/testing", True),
				array("*bla*/**/bla/*", "XXXblaXXXX/testing/testing/bla/testing/testing", True),

				array("/x/x/**/bla", "/x/x/x/", True),

				array("", "", True)
		);
	}

	public function extractPathWithinPatternDataProvider() {
		return array(
				array("/docs/commit.html", "/docs/commit.html", ""),

				array("/docs/*", "/docs/cvs/commit", "cvs/commit"),
				array("/docs/cvs/*.html", "/docs/cvs/commit.html", "commit.html"),
				array("/docs/**", "/docs/cvs/commit", "cvs/commit"),
				array("/docs/**/*.html", "/docs/cvs/commit.html", "cvs/commit.html"),
				array("/docs/**/*.html", "/docs/commit.html", "commit.html"),
				array("/*.html", "/commit.html", "commit.html"),
				array("/*.html", "/docs/commit.html", "docs/commit.html"),
				array("*.html", "/commit.html", "/commit.html"),
				array("*.html", "/docs/commit.html", "/docs/commit.html"),
				array("**/*.*", "/docs/commit.html", "/docs/commit.html"),
				array("*", "/docs/commit.html", "/docs/commit.html"),

				array("/d?cs/*", "/docs/cvs/commit", "docs/cvs/commit"),
				array("/docs/c?s/*.html", "/docs/cvs/commit.html", "cvs/commit.html"),
				array("/d?cs/**", "/docs/cvs/commit", "docs/cvs/commit"),
				array("/d?cs/**/*.html", "/docs/cvs/commit.html", "docs/cvs/commit.html")
		);
	}

	public function uniqueDeliminatorDataProvider() {
		return array(
			// test exact matching
				array("test", "test", True),
				array(".test", ".test", True),
				array(".test/jpg", "test/jpg", False),
				array("test", ".test", False),
				array(".test", "test", False),

			// test matching with ?'s
				array("t?st", "test", True),
				array("??st", "test", True),
				array("tes?", "test", True),
				array("te??", "test", True),
				array("?es?", "test", True),
				array("tes?", "tes", False),
				array("tes?", "testt", False),
				array("tes?", "tsst", False),

			// test matchin with *'s
				array("*", "test", True),
				array("test*", "test", True),
				array("test*", "testTest", True),
				array("*test*", "AnothertestTest", True),
				array("*test", "Anothertest", True),
				array("*/*", "test/", True),
				array("*/*", "test/test", True),
				array("*/*", "test/test/test", True),
				array("test*aaa", "testblaaaa", True),
				array("test*", "tst", False),
				array("test*", "tsttest", False),
				array("*test*", "tsttst", False),
				array("*test", "tsttst", False),
				array("*/*", "tsttst", False),
				array("test*aaa", "test", False),
				array("test*aaa", "testblaaab", False),

			// test matching with ?'s and .'s
				array(".?", ".a", True),
				array(".?.a", ".a.a", True),
				array(".a.?", ".a.b", True),
				array(".??.a", ".aa.a", True),
				array(".a.??", ".a.bb", True),
				array(".?", ".a", True),

			// test matching with **'s
				array(".**", ".testing.testing", True),
				array(".*.**", ".testing.testing", True),
				array(".**.*", ".testing.testing", True),
				array(".bla.**.bla", ".bla.testing.testing.bla", True),
				array(".bla.**.bla", ".bla.testing.testing.bla.bla", True),
				array(".**.test", ".bla.bla.test", True),
				array(".bla.**.**.bla", ".bla.bla.bla.bla.bla.bla", True),
				array(".bla*bla.test", ".blaXXXbla.test", True),
				array(".*bla.test", ".XXXbla.test", True),
				array(".bla*bla.test", ".blaXXXbl.test", False),
				array(".*bla.test", "XXXblab.test", False),
				array(".*bla.test", "XXXbl.test", False)
		);
	}
} 