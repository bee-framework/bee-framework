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
/*  0 */	array("test", "test", True), // #0
/*  1 */	array("/test", "/test", True), // #1
/*  2 */	array("/test.jpg", "test.jpg", False), // #2
/*  3 */	array("test", "/test", False), // #3
/*  4 */	array("/test", "test", False), // #4
/*    */	// test matching with ?'s
/*  5 */	array("t?st", "test", True), // #5
/*  6 */	array("??st", "test", True), // #6
/*  7 */	array("tes?", "test", True), // #7
/*  8 */	array("te??", "test", True), // #8
/*  9 */	array("?es?", "test", True), // #9
/* 10 */	array("tes?", "tes", False), // #10
/* 11 */	array("tes?", "testt", False),
/* 12 */	array("tes?", "tsst", False),
/*    */	// test matchin with *'s
/* 13 */	array("*", "test", True),
/* 14 */	array("test*", "test", True),
/* 15 */	array("test*", "testTest", True), // #15
/* 16 */	array("test/*", "test/Test", True),
/* 17 */	array("test/*", "test/t", True),
/* 18 */	array("test/*", "test/", True),
/* 19 */	array("*test*", "AnothertestTest", True),
/* 20 */	array("*test", "Anothertest", True), // #20
/* 21 */	array("*.*", "test.", True),
/* 22 */	array("*.*", "test.test", True),
/* 23 */	array("*.*", "test.test.test", True),
/* 24 */	array("test*aaa", "testblaaaa", True),
/* 25 */	array("test*", "tst", False), // #25
/* 26 */	array("test*", "tsttest", False),
/* 27 */	array("test*", "test/", False),
/* 28 */	array("test*", "test/t", False),
/* 29 */	array("test/*", "test", False),
/* 30 */	array("*test*", "tsttst", False), // #30
/* 31 */	array("*test", "tsttst", False),
/* 32 */	array("*.*", "tsttst", False),
/* 33 */	array("test*aaa", "test", False),
/* 34 */	array("test*aaa", "testblaaab", False),
/*    */	// test matching with ?'s and /'s
/* 35 */	array("/?", "/a", True), // #35
/* 36 */	array("/?/a", "/a/a", True),
/* 37 */	array("/a/?", "/a/b", True),
/* 38 */	array("/??/a", "/aa/a", True),
/* 39 */	array("/a/??", "/a/bb", True),
/* 40 */	array("/?", "/a", True), // #40
/*    */	// test matching with **'s
/* 41 */	array("/**", "/testing/testing", True),						// #41
/* 42 */	array("/*/**", "/testing/testing", True),					// #42
/* 43 */	array("/**/*", "/testing/testing", True),					// #43
/* 44 */	array("/bla/**/bla", "/bla/testing/testing/bla", True),		// #44
/* 45 */	array("/bla/**/bla", "/bla/testing/testing/bla/bla", True), // #45
/* 46 */	array("/**/test", "/bla/bla/test", True),					// #48
/* 47 */	array("/**/test", "/test", True),							// #47
/* 48 */	array("/bla/**/**/bla", "/bla/bla/bla/bla/bla/bla", True),	// #48
/* 49 */	array("/bla*bla/test", "/blaXXXbla/test", True),			// #49
/* 50 */	array("/*bla/test", "/XXXbla/test", True),					// #50
/* 51 */	array("/bla*bla/test", "/blaXXXbl/test", False),			// #51
/* 52 */	array("/*bla/test", "XXXblab/test", False),					// #52
/* 53 */	array("/*bla/test", "XXXbl/test", False),					// #53
/* 54 */	array("/*bla/test", "/bla/test", true),						// #54
/* 55 */	array("/????", "/bala/bla", False),
/* 56 */	array("/**/*bla", "/bla/bla/bla/bbb", False),
/* 57 */	array("/*bla*/**/bla/**", "/XXXblaXXXX/testing/testing/bla/testing/testing/", True), // #55
/* 58 */	array("/*bla*/**/bla/*", "/XXXblaXXXX/testing/testing/bla/testing", True),
/* 59 */	array("/*bla*/**/bla/**", "/XXXblaXXXX/testing/testing/bla/testing/testing", True),
/* 60 */	array("/*bla*/**/bla/**", "/XXXblaXXXX/testing/testing/bla/testing/testing.jpg", True),
/* 61 */	array("*bla*/**/bla/**", "XXXblaXXXX/testing/testing/bla/testing/testing/", True),
/* 62 */	array("*bla*/**/bla/*", "XXXblaXXXX/testing/testing/bla/testing", True), // #60
/* 63 */	array("*bla*/**/bla/**", "XXXblaXXXX/testing/testing/bla/testing/testing", True),
/* 64 */	array("*bla*/**/bla/*", "XXXblaXXXX/testing/testing/bla/testing/testing", False),
/* 65 */	array("/x/x/**/bla", "/x/x/x/", False),
/* 66 */	array("/x/x/**/bla", "/x/x/bla", true),
/*    */	// trailing slash tests
/* 67 */	array("/x/x/**/bla/", "/x/x/bla/", true),
/* 68 */	array("/x/x/**/bla/", "/x/x/bla", true),
/* 69 */	array("/x/x/**/bla", "/x/x/bla/", false),
/* 70 */	array("/x/x/**/bla", "/x/x/bla", true),
/* 71 */	array("/x/x/**/bla//", "/x/x/bla/", true),// fails
/* 72 */	array("/x/x/**/bla//", "/x/x/bla", false),

/* 73 */	array("/test/**", "/test", true),

/* 74 */	array("/**", "", true), // fails
/* 75 */	array("", "", true)//,

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