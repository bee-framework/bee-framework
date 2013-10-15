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
 * Date: 06.07.11
 * Time: 23:54
 */
 
class Bee_Weaving_Store_Plain implements Bee_Weaving_IEnhancedClassesStore {

	/**
	 * @var string
	 */
	private $enhancedClassesLocation;

	public function __construct($enhancedClassesLocation) {
		$this->enhancedClassesLocation = $enhancedClassesLocation;
	}

	/**
	 * @param string $className
	 * @return bool
	 */
	public function hasStoredClass($className) {
		return file_exists($this->getClassLocation($className));
	}

	/**
	 * @param string $className
	 * @param string $classSource
	 * @return void
	 */
	public function storeClass($className, $classSource) {
		file_put_contents($this->getClassLocation($className), '<?php ' . $classSource);
	}

	/**
	 * @param $className
	 * @return bool
	 */
	public function loadClass($className) {
		include_once $this->getClassLocation($className);
		return class_exists($className, false) || interface_exists($className, false);
	}

	private function getClassLocation($className) {
		return $this->enhancedClassesLocation . DIRECTORY_SEPARATOR . $className . '.php';
	}
}
