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
use Bee\Cache\IProvider;

/**
 * User: mp
 * Date: 02.07.11
 * Time: 15:16
 */

class Bee_Cache_Provider_FileTest extends PHPUnit_Framework_TestCase {

//    /**
//   	 * Initialize the cache provider if necessary. Called once per request, before the first cache access is made.
//   	 * @abstract
//   	 * @return void
//   	 */
//   	public function init();
//
//   	/**
//   	 * Store the value in the cache under the given key. If given, a modification timestamp will be stored with the value
//   	 * and can be retrieved using getLastUpdateTimestamp(). If an UNIX timestamp is given for etime, the cache entry will
//   	 * expire at that time.
//   	 * @abstract
//   	 * @param $key
//   	 * @param $value
//   	 * @param int $etime
//   	 * @return void
//   	 */
//   	public function store($key, &$value, $etime = 0);
//
//   	/**
//   	 * @abstract
//   	 * @param $key string
//   	 * @return bool
//   	 */
//   	public function exists($key);
//
//   	/**
//   	 * @abstract
//   	 * @param $key string
//   	 * @return mixed
//   	 */
//   	public function retrieve($key);
//
//   	public function evict($key);
//
//   	public function listKeys();
//
//   	public function clearCache();
//
//   	/**
//   	 * Shut down the cache provider at the end of a request.
//   	 * @abstract
//   	 * @return void
//   	 */
//   	public function shutdown();


    /**
     * @var IProvider
     */
    private $provider;

    protected function setUp() {
//        $provider = new Strings()
    }

    protected function tearDown() {
        // delete file here
    }

	/**
	 * @test
	 */
	public function incomplete() {
		$this->markTestIncomplete();
	}

}