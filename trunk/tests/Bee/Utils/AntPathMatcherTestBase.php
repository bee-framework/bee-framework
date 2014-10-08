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

/**
 * Class AntPathMatcherTestBase
 * @package Bee\Utils
 */
class AntPathMatcherTestBase extends AntPathDataProvider {

	/**
	 * @var AntPathMatcher
	 */
	protected $pathMatcher;

	/**
	 * @test
	 * @dataProvider matchDataProvider
	 * @param $pattern
	 * @param $path
	 * @param $result
	 */
    public function match($pattern, $path, $result) {
        $this->assertEquals($result, $this->pathMatcher->match($pattern, $path));
    }
}