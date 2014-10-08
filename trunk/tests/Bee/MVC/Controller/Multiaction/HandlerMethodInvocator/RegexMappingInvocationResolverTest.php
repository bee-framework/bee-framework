<?php
namespace Bee\MVC\Controller\Multiaction\HandlerMethodInvocator;
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
use Bee\Beans\PropertyEditor\PropertyEditorRegistry;
use Bee\MVC\HttpRequestMock;
use PHPUnit_Framework_TestCase;

/**
 * Class RegexMappingInvocationResolver
 * @package Bee\MVC\Controller\Multiaction\HandlerMethodInvocator
 */
class RegexMappingInvocationResolverTest extends PHPUnit_Framework_TestCase {

	/**
	 * @test
	 * @dataProvider matchDataProvider
	 * @param RegexMappingInvocationResolver $resolver
	 * @param $pathInfo
	 * @param $expectedAntPath
	 * @param array $expectedParams
	 */
	public function testMapping(RegexMappingInvocationResolver $resolver, $pathInfo, $expectedAntPath, $expectedParams = array()) {
		$request = new HttpRequestMock($pathInfo);
		$invocation = $resolver->getInvocationDefinition($request);
		$this->assertTrue(!is_null($invocation));
		$this->assertEquals($expectedAntPath, $invocation->getAntPathPattern());

		$res = array_intersect_key($invocation->getParamValues(), array_flip($invocation->getUrlParameterPositions()));
		$this->assertEquals($expectedParams, array_merge($res)); // param neu durchnummerieren
	}


	public function matchDataProvider() {
		$testMapping = $this->provideTestRegexMappingInvocationResolver1();
		return array(
				array($testMapping, '/complicated', '/complicated'), // # 00
				array($testMapping, '/complicated/', '/complicated/**'),
				array($testMapping, '/complicated/hund', '/complicated/{string1}', array('hund')),

				array($testMapping, '/complicated/more/hund/complicator/hermelin/hase', '/complicated/more/{string1}/complicator/{string2}/**', array('hund', 'hermelin')),
				array($testMapping, '/complicated/more/hund/complicator/hermelin/hase/dachs', '/complicated/more/{string1}/complicator/{string2}/**', array('hund', 'hermelin')),

				array($testMapping,
						'/complicated/more/hund/complicator/hase',
						'/complicated/more/{string1}/complicator/{string2}',
						array('hund', 'hase')),														// # 05
				array($testMapping,
						'/complicated/more/complicator/hund',
						'/complicated/more/**/complicator/{string2}',
						array('hund')),
				array($testMapping,
						'/complicated/more/hund/hermelin/complicator/hase',
						'/complicated/more/**/complicator/{string2}',
						array('hase')),
				array($testMapping,
						'/hund/complicated/more/hund/complicator/hermelin/hase',
						'/**/complicated/more/{string1}/complicator/{string2}/**',
						array('hund', 'hermelin')),
				array($testMapping,
						'/x/complicated/more/complicator/hermelin/hase',
						'/**/complicated/more/**/complicator/{string2}/**',
						array('hermelin')),
				array($testMapping,
						'/hase/complicated/more/complicator/hermelin/x',
						'/**/complicated/more/**/complicator/{string2}/**',
						array('hermelin')),																		// # 10
				array($testMapping,
						'/x/y/z/complicated/more/hund/complicator/hermelin/hase',
						'/**/complicated/more/{string1}/complicator/{string2}/**',
						array('hund',
								'hermelin')),
				array($testMapping,
						'/x/complicated/more/y/complicator/hermelin/hase',
						'/**/complicated/more/{string1}/complicator/{string2}/**',
						array('y', 'hermelin')),
				array($testMapping,
						'/complicated/more/x/y/z/complicator/hermelin/hase',
						'/complicated/more/**/complicator/{string2}/**',
						array('hermelin')),
				array($testMapping,
						'/complicated/more/x/y/z/compliment/hermelin/hase',
						'/complicated/more/**/compl*/{string2}/**',
						array('hermelin')),
				array($testMapping, '', '/**')
		);
	}

	public function provideTestRegexMappingInvocationResolver1() {
		$method = new \ReflectionMethod('Bee\MVC\Controller\Multiaction\HandlerMethodInvocator\HandlerMock', 'methodA');

		$testPatterns1 = array();
		$testPatterns1['/complicated'] = $method;
		$testPatterns1['/complicated/**'] = $method;
		$testPatterns1['/complicated/more'] = $method;
		$testPatterns1['/complicated/{string1}'] = $method;
		$testPatterns1['/complicated/more/music'] = $method;
		$testPatterns1['/complicated/more/{string1}/music'] = $method;
		$testPatterns1['/complicated/more/{string1}/{string2}'] = $method;
		$testPatterns1['/complicated/more/{string1}/{string2}/{string3}'] = $method;
		$testPatterns1['/complicated/more/{string1}/complicator/{string2}'] = $method;
		$testPatterns1['/complicated/{string1}/complicator/{string2}'] = $method;
		$testPatterns1['/complicated/**/complicator/{string2}'] = $method;
		$testPatterns1['/complicated/more/**/complicator/{string2}'] = $method;
		$testPatterns1['/complicated/more/**'] = $method;
		$testPatterns1['/complicated/{string1}/**'] = $method;
		$testPatterns1['/complicated/more/music/**'] = $method;
		$testPatterns1['/complicated/more/{string1}/music/**'] = $method;
		$testPatterns1['/complicated/more/{string1}/{string2}/**'] = $method;
		$testPatterns1['/complicated/more/{string1}/{string2}/{string3}/**'] = $method;
		$testPatterns1['/complicated/more/{string1}/complicator/{string2}/**'] = $method;
		$testPatterns1['/complicated/{string1}/complicator/{string2}/**'] = $method;
		$testPatterns1['/complicated/**/complicator/{string2}/**'] = $method;
		$testPatterns1['/complicated/more/**/complicator/{string2}/**'] = $method;

		$testPatterns1['/**/complicated'] = $method;
		$testPatterns1['/**/complicated/**'] = $method;
		$testPatterns1['/**/complicated/more'] = $method;
		$testPatterns1['/**/complicated/{string1}'] = $method;
		$testPatterns1['/**/complicated/more/music'] = $method;
		$testPatterns1['/**/complicated/more/{string1}/music'] = $method;
		$testPatterns1['/**/complicated/more/{string1}/{string2}'] = $method;
		$testPatterns1['/**/complicated/more/{string1}/{string2}/{string3}'] = $method;
		$testPatterns1['/**/complicated/more/{string1}/complicator/{string2}'] = $method;
		$testPatterns1['/**/complicated/{string1}/complicator/{string2}'] = $method;
		$testPatterns1['/**/complicated/**/complicator/{string2}'] = $method;
		$testPatterns1['/**/complicated/more/**/complicator/{string2}'] = $method;
		$testPatterns1['/**/complicated/more/**'] = $method;
		$testPatterns1['/**/complicated/{string1}/**'] = $method;
		$testPatterns1['/**/complicated/more/music/**'] = $method;
		$testPatterns1['/**/complicated/more/{string1}/music/**'] = $method;
		$testPatterns1['/**/complicated/more/{string1}/{string2}/**'] = $method;
		$testPatterns1['/**/complicated/more/{string1}/{string2}/{string3}/**'] = $method;
		$testPatterns1['/**/complicated/more/{string1}/complicator/{string2}/**'] = $method;
		$testPatterns1['/**/complicated/{string1}/complicator/{string2}/**'] = $method;
		$testPatterns1['/**/complicated/**/complicator/{string2}/**'] = $method;
		$testPatterns1['/**/complicated/more/**/complicator/{string2}/**'] = $method;

		$testPatterns1['/**/more'] = $method;
		$testPatterns1['/**/{string1}'] = $method;
		$testPatterns1['/**/more/music'] = $method;
		$testPatterns1['/**/more/{string1}/music'] = $method;
		$testPatterns1['/**/more/{string1}/{string2}'] = $method;
		$testPatterns1['/**/more/{string1}/{string2}/{string3}'] = $method;
		$testPatterns1['/**/more/{string1}/complicator/{string2}'] = $method;
		$testPatterns1['/**/{string1}/complicator/{string2}'] = $method;
		$testPatterns1['/**/**/complicator/{string2}'] = $method;
		$testPatterns1['/**/more/**/complicator/{string2}'] = $method;
		$testPatterns1['/**/more/**'] = $method;
		$testPatterns1['/**/{string1}/**'] = $method;
		$testPatterns1['/**/more/music/**'] = $method;
		$testPatterns1['/**/more/{string1}/music/**'] = $method;
		$testPatterns1['/**/more/{string1}/{string2}/**'] = $method;
		$testPatterns1['/**/more/{string1}/{string2}/{string3}/**'] = $method;
		$testPatterns1['/**/more/{string1}/complicator/{string2}/**'] = $method;
		$testPatterns1['/**/{string1}/complicator/{string2}/**'] = $method;
		$testPatterns1['/**/complicator/{string2}/**'] = $method;
		$testPatterns1['/**/more/**/complicator/{string2}/**'] = $method;

		$testPatterns1['/**'] = $method;
		$testPatterns1['/*/more'] = $method;
		$testPatterns1['/*/{string1}'] = $method;
		$testPatterns1['/*/more/music'] = $method;
		$testPatterns1['/*/more/{string1}/music'] = $method;
		$testPatterns1['/*/more/{string1}/{string2}'] = $method;
		$testPatterns1['/*/more/{string1}/{string2}/{string3}'] = $method;
		$testPatterns1['/*/more/{string1}/complicator/{string2}'] = $method;
		$testPatterns1['/*/{string1}/complicator/{string2}'] = $method;
		$testPatterns1['/*/**/complicator/{string2}'] = $method;
		$testPatterns1['/*/more/**/complicator/{string2}'] = $method;
		$testPatterns1['/*/more/**'] = $method;
		$testPatterns1['/*/{string1}/**'] = $method;
		$testPatterns1['/*/more/music/**'] = $method;
		$testPatterns1['/*/more/{string1}/music/**'] = $method;
		$testPatterns1['/*/more/{string1}/{string2}/**'] = $method;
		$testPatterns1['/*/more/{string1}/{string2}/{string3}/**'] = $method;
		$testPatterns1['/*/more/{string1}/complicator/{string2}/**'] = $method;
		$testPatterns1['/*/{string1}/complicator/{string2}/**'] = $method;
		$testPatterns1['/*/complicator/{string2}/**'] = $method;
		$testPatterns1['/*/more/**/complicator/{string2}/**'] = $method;

		$testPatterns1['/complicated/more/**/compl*/{string2}/**'] = $method;

		return new RegexMappingInvocationResolver($testPatterns1, new PropertyEditorRegistry());
	}
}

class HandlerMock {

	/**
	 * @param string $string1
	 * @param string $string2
	 * @param string $string3
	 */
	public function methodA($string1, $string2, $string3) {

	}
}