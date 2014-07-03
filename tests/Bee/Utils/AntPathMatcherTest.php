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
use Bee\Utils\AntPathDataProvider;
use Bee\Utils\AntPathMatcher;

/**
 * User: mp
 * Date: 02.07.11
 * Time: 15:00
 */
class Bee_Utils_AntPathMatcherTest extends AntPathDataProvider {

    /**
     * @var AntPathMatcher
     */
    private $pathMatcher;

    protected function setUp() {
        $this->pathMatcher = new AntPathMatcher();
    }

    /**
     * @test
     * @dataProvider matchDataProvider
     */
    public function match($pattern, $path, $result) {
        $this->assertEquals($result, $this->pathMatcher->match($pattern, $path));
    }

    /**
     * @test
     * @dataProvider withMatchStartDataProvider
     */
    public function withMatchStart($pattern, $path, $result) {
        $this->assertEquals($result, $this->pathMatcher->matchStart($pattern, $path));
    }

    /**
     * @test
     * @dataProvider uniqueDeliminatorDataProvider
     */
    public function uniqueDeliminator($pattern, $path, $result) {
        $this->pathMatcher->setPathSeparator(".");
        $this->assertEquals($result, $this->pathMatcher->match($pattern, $path));
    }

    /**
     * @test
     * @dataProvider extractPathWithinPatternDataProvider
     */
    public function extractPathWithinPattern($pattern, $path, $result) {
        $this->assertEquals($result, $this->pathMatcher->extractPathWithinPattern($pattern, $path));
    }
}
