<?php

namespace Bee\MVC\Controller\Multiaction\HandlerMethodInvocator;

use Bee\Beans\PropertyEditor\PropertyEditorRegistry;
use Bee\Utils\ITypeDefinitions;
use ReflectionClass;
use ReflectionMethod;


/**
 * Class HandlerMethodMetadata
 * @package Bee\MVC\Controller\Multiaction\HandlerMethodInvocator
 */
class HandlerMethodMetadata {

	const CLASS_USES_MATCH = '#^\s*use\s+((?:\w+\\\\)*(\w+))(?: as (\w+))?;\s*$#m';
	const DOCBLOCK_PARAM_TYPE_HINT_MATCH = '#\*\s+@param\s+([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)#';

	/**
	 * @var array
	 */
	private static $importMap = array();

	/**
	 * @var ReflectionMethod
	 */
	private $method;

	/**
	 * @var
	 */
	private $typeMap;

	/**
	 * @var array
	 */
	private $requestParamPos;

	/**
	 * @var array
	 */
	private $parameterPositions;

	/**
	 * @param ReflectionMethod $method
	 * @param PropertyEditorRegistry $propertyEditorRegistry
	 */
	function __construct(ReflectionMethod $method, PropertyEditorRegistry $propertyEditorRegistry) {
		$this->method = $method;

		$typeNameMap = self::getDocBlockTypeHints($method, $propertyEditorRegistry);
		$this->typeMap = array();
		$this->requestParamPos = array();
		$this->parameterPositions = array();

		foreach ($method->getParameters() as $param) {
			$typeName = array_key_exists($param->getName(), $typeNameMap) ?
					$typeNameMap[$param->getName()] :
					(!is_null($param->getClass()) ? $param->getClass()->getName() : ITypeDefinitions::STRING);
			if ($typeName == 'Bee_MVC_IHttpRequest') {
				$this->requestParamPos[$param->getPosition()] = true;
			}
			$this->typeMap[$param->getName()] = $typeName;
			$this->typeMap[$param->getPosition()] = $typeName;
			$this->parameterPositions[$param->getName()] = $param->getPosition();
		}
	}

	/**
	 * @return ReflectionMethod
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 * @return array
	 */
	public function getRequestParamPos() {
		return $this->requestParamPos;
	}

	/**
	 * @return mixed
	 */
	public function getTypeMap() {
		return $this->typeMap;
	}

	/**
	 * @param $pos
	 * @return mixed
	 */
	public function getTypeMapping($pos) {
		return $this->typeMap[$pos];
	}

	/**
	 * @return array
	 */
	public function getParameterPositions() {
		return $this->parameterPositions;
	}

	/**
	 * @param ReflectionMethod $method
	 * @param PropertyEditorRegistry $propertyEditorRegistry
	 * @return array
	 */
	protected static function getDocBlockTypeHints(ReflectionMethod $method, PropertyEditorRegistry $propertyEditorRegistry) {
		$importMap = self::getImportMap($method->getDeclaringClass());
		$namespace = $method->getDeclaringClass()->getNamespaceName();
		$matches = array();
		preg_match_all(self::DOCBLOCK_PARAM_TYPE_HINT_MATCH, $method->getDocComment(), $matches);
		$types = $matches[1];
		array_walk($types, function (&$value) use ($importMap, $namespace, $propertyEditorRegistry) {
			if (array_key_exists($value, $importMap)) {
				$value = $importMap[$value];
			} else if(!$propertyEditorRegistry->editorExists($value)) {
				$value = $namespace . '\\' . $value;
			}
			// test if property editor exists
			$propertyEditorRegistry->getEditor($value);
		});
		return array_combine($matches[2], $types);
	}

	/**
	 * @param ReflectionClass $class
	 * @return mixed
	 */
	protected static function getImportMap(ReflectionClass $class) {
		$className = $class->getName();
		if (!array_key_exists($className, self::$importMap)) {
			$fileContent = file_get_contents($class->getFileName());
			$matches = array();
			preg_match_all(self::CLASS_USES_MATCH, $fileContent, $matches, PREG_SET_ORDER);
			foreach ($matches as $match) {
				self::$importMap[$className][$match[count($match) - 1]] = $match[1];
			}
		}
		return self::$importMap[$className];
	}
} 