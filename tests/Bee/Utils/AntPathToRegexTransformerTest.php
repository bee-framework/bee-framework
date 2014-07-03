<?php
use Bee\Utils\AntPathDataProvider;
use Bee\Utils\AntPathToRegexTransformer;

/**
 * Class AntPathToRegexTransformerTest
 */
class AntPathToRegexTransformerTest extends AntPathDataProvider {

	/**
	 * @test
	 * @dataProvider matchDataProvider
	 */
	public function testGetRegexForSimplePattern($pattern, $path, $result) {
		$regex = AntPathToRegexTransformer::getRegexForSimplePattern($pattern);
		$matches = array();
		echo $pattern . "\n";
		echo $path . "\n";
		echo $regex . "\n";
		$this->assertEquals($result, preg_match_all($regex, $path, $matches) === 1);
	}

	/**
	 * @test
	 * @dataProvider matchDataProvider
	 */
	public function testGetRegexForParametrizedPatternWithUnparametrizedPattern($pattern, $path, $result) {
		$regex = AntPathToRegexTransformer::getRegexForParametrizedPattern($pattern, array());
		$matches = array();
		$this->assertEquals($result, preg_match_all($regex, $path, $matches) === 1);
	}

	/**
	 * @test
	 * @dataProvider parametrizedMatchDataProvider
	 */
	public function testGetRegexForParametrizedPattern($pattern, $path, array $typeMap, $result, array $positionMapResult = null) {
		$positionMap = array();
		$regex = AntPathToRegexTransformer::getRegexForParametrizedPattern($pattern, $typeMap, $positionMap);
		$matches = array();
		$this->assertEquals($result, preg_match_all($regex, $path, $matches) === 1);
		if(!is_null($positionMapResult)) {
			$this->assertEquals($positionMapResult, $positionMap);
		}
	}

	/**
	 *
	 */
	public function parametrizedMatchDataProvider() {
		return array(
			array('/test', '/test', array(), true),																												// 0
			array('/test/{param1}', '/test/123', array('param1' => Bee_Utils_ITypeDefinitions::INT), true, array( 0 => 'param1')),								// 1
			array('/test/{param1}', '/test/id15', array('param1' => Bee_Utils_ITypeDefinitions::INT), false),													// 2
			array('/test/id{param1}', '/test/id15', array('param1' => Bee_Utils_ITypeDefinitions::INT), true, array( 0 => 'param1')),							// 3
			array('/test/id{par-am1}', '/test/id15', array('param1' => Bee_Utils_ITypeDefinitions::INT), false),												// 4
			array('/test/id{1}/{2}', '/test/id15/true', array(1 => Bee_Utils_ITypeDefinitions::INT, 2 => Bee_Utils_ITypeDefinitions::BOOLEAN), true, array( 0 => '1', 1 => 2 )), 			// 5
			array('/test/id{1}/{2}', '/test/id15/knuth', array(1 => Bee_Utils_ITypeDefinitions::INT, 2 => Bee_Utils_ITypeDefinitions::BOOLEAN), false),			// 6
			array('/test/id{1}/{2}', '/test/id15/knuth', array(1 => Bee_Utils_ITypeDefinitions::INT, 2 => Bee_Utils_ITypeDefinitions::STRING), true, array( 0 => '1', 1 => 2 )),			// 7
			array('/test/id{2}/{1}', '/test/id15/knuth', array(1 => Bee_Utils_ITypeDefinitions::INT, 2 => Bee_Utils_ITypeDefinitions::STRING), false),			// 8
			array('/test/id{2}/{1}', '/test/id15/knuth', array(1 => Bee_Utils_ITypeDefinitions::STRING, 2 => Bee_Utils_ITypeDefinitions::INT), true, array( 0 => 2, 1 => 1 )),			// 9
			array('/test/id{1}/{2}', '/test/id15/knuth/foo', array(1 => Bee_Utils_ITypeDefinitions::INT, 2 => Bee_Utils_ITypeDefinitions::STRING), false),		// 10
			array('/test/id{1}/{2}**', '/test/id15/knuth/foo', array(1 => Bee_Utils_ITypeDefinitions::INT, 2 => Bee_Utils_ITypeDefinitions::STRING), true),		// 11
			array('/test/id{1}/{2}/**', '/test/id15/knu-th/foo', array(1 => Bee_Utils_ITypeDefinitions::INT, 2 => Bee_Utils_ITypeDefinitions::STRING), true),	// 12

			array('/*/test', '/test', array(), false),																											// 12
			array('/*/test/{param1}', '/test/123', array('param1' => Bee_Utils_ITypeDefinitions::INT), false),													// 13
			array('/*/test/{param1}', '/test/id15', array('param1' => Bee_Utils_ITypeDefinitions::INT), false),													// 14
			array('/*/test/id{param1}', '/test/id15', array('param1' => Bee_Utils_ITypeDefinitions::INT), false),												// 15
			array('/*/test/id{1}/{2}', '/test/id15/true', array(1 => Bee_Utils_ITypeDefinitions::INT, 2 => Bee_Utils_ITypeDefinitions::BOOLEAN), false),		// 16
			array('/*/test/id{1}/{2}', '/test/id15/knuth', array(1 => Bee_Utils_ITypeDefinitions::INT, 2 => Bee_Utils_ITypeDefinitions::BOOLEAN), false),		// 17

			array('/*/test', '/ab/test', array(), true),																// 17
			array('/*/test/{param1}', '/at/test/123', array('param1' => Bee_Utils_ITypeDefinitions::INT), true),		// 18
			array('/*/test/{param1}', '/at/test/id15', array('param1' => Bee_Utils_ITypeDefinitions::INT), false),		// 19
			array('/*/test/id{param1}', '/at/test/id15', array('param1' => Bee_Utils_ITypeDefinitions::INT), true),		// 20
			array('/*/test/id{1}/{2}', '/at/test/id15/true', array(1 => Bee_Utils_ITypeDefinitions::INT, 2 => Bee_Utils_ITypeDefinitions::BOOLEAN), true),		// 21
			array('/*/test/id{1}/{2}', '/at/test/id15/knuth', array(1 => Bee_Utils_ITypeDefinitions::INT, 2 => Bee_Utils_ITypeDefinitions::BOOLEAN), false),	// 22

			array('/**/test', '/ab/test', array(), true),																// 23
			array('/**/test/{param1}', '/at/test/123', array('param1' => Bee_Utils_ITypeDefinitions::INT), true),		// 24
			array('/**/test/{param1}', '/at/test/id15', array('param1' => Bee_Utils_ITypeDefinitions::INT), false),		// 25
			array('/**/test/id{param1}', '/at/test/id15', array('param1' => Bee_Utils_ITypeDefinitions::INT), true),	// 26
			array('/**/test/id{1}/{2}', '/at/test/id15/true', array(1 => Bee_Utils_ITypeDefinitions::INT, 2 => Bee_Utils_ITypeDefinitions::BOOLEAN), true),		// 27
			array('/**/test/id{1}/{2}', '/at/test/id15/knuth', array(1 => Bee_Utils_ITypeDefinitions::INT, 2 => Bee_Utils_ITypeDefinitions::BOOLEAN), false),	// 28

			array('/**/test', '/ab/cd/test', array(), true),															// 29
			array('/**/test/{param1}', '/at/test/123', array('param1' => Bee_Utils_ITypeDefinitions::INT), true),		// 30
			array('/**/test/{param1}', '/at/test/id15', array('param1' => Bee_Utils_ITypeDefinitions::INT), false),		// 31
			array('/**/test/id{param1}', '/at/test/id15', array('param1' => Bee_Utils_ITypeDefinitions::INT), true),	// 32
			array('/**/test/id{1}/{2}', '/at/test/id15/true', array(1 => Bee_Utils_ITypeDefinitions::INT, 2 => Bee_Utils_ITypeDefinitions::BOOLEAN), true),		// 33
			array('/**/test/id{1}/{2}', '/at/test/id15/knuth', array(1 => Bee_Utils_ITypeDefinitions::INT, 2 => Bee_Utils_ITypeDefinitions::BOOLEAN), false) 	// 34
		);
	}
}