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
use Bee\Cache\Manager;

/**
 * User: mp
 * Date: 07.07.11
 * Time: 01:00
 */
 
class Bee_Weaving_Store_Cache implements Bee_Weaving_IEnhancedClassesStore {

	/**
	 * @param string $className
	 * @return bool
	 */
	function hasStoredClass($className) {
		return Manager::exists($this->getKeyForClass($className));
	}

	/**
	 * @param string $className
	 * @param string $classSource
	 * @return void
	 */
	function storeClass($className, $classSource) {
		Manager::store($this->getKeyForClass($className), $classSource);
	}

	/**
	 * @param $className
	 * @return bool
	 */
	function loadClass($className) {
		if(class_exists($className) || interface_exists($className)) {
			return false;
		}
		$src = Manager::getProvider()->retrieve($this->getKeyForClass($className));
		if($src !== false) {
			return eval($src) !== false;
		}
		return false;
	}

	private function getKeyForClass($className) {
		return __CLASS__.$className;
	}
}
