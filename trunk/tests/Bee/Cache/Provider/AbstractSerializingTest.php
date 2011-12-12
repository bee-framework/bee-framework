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

class Bee_Cache_Provider_AbstractSerializingTest extends PHPUnit_Framework_TestCase {

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
     * @var Bee_Cache_IProvider
     */
    private $provider;

    protected function setUp() {
        $this->provider = new Bee_Cache_Provider_AbstractSerializingMock(array(
            'foo' => serialize('bar'),
            'foo__ETIME__' => time()
        ));
        $this->provider->init();
    }

    /**
     * @test
     */
    public function testInitNonEmptyCache() {
        $this->assertEquals('bar', $this->provider->retrieve('foo'));
    }

    /**
     * @test
     */
    public function testStore() {
        // simple string
        $value1 = 'test store value';
        $this->provider->store('test-store-1', $value1);
        $this->assertEquals($value1, $this->provider->retrieve('test-store-1'));

        // complex array
        $value2 = array(
            'level-1-1' => 'level-1-1 value',
            'level-1-2' => 17,
            'level-1-3' => new stdClass(),
            'level-1-4' => array(
                'level-2-1' => new stdClass(),
                'level-2-2' => new stdClass(),
                'level-2-3' => 'level-2-3 value',
                'level-2-3' => array(
                    'value-true' => true,
                    'value-integer' => 18,
                    'value-null' => null
                )),
            'level-1-5' => 'the end'
        );
        $this->provider->store('test-store-2', $value2);
        $this->assertEquals($value2, $this->provider->retrieve('test-store-2'));

        // modify complex array
        $value2['level-1-4']['level-2-1'] = 15;
        $this->assertNotEquals($value2, $this->provider->retrieve('test-store-2'));

        // check old value
        $this->assertEquals($value1, $this->provider->retrieve('test-store-1'));

        // test init value
        $this->assertEquals('bar', $this->provider->retrieve('foo'));
    }

}


class Bee_Cache_Provider_AbstractSerializingMock extends Bee_Cache_Provider_AbstractSerializing {

    private $cacheArray = array();

    public function __construct(array $array=array()) {
        $this->cacheArray = $array;
    }

    protected function &loadCacheArray() {
        return $this->cacheArray;
    }

    protected function storeCacheArray(&$array) {
        $this->cacheArray = $array;
    }

}