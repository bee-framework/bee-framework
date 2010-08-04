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
 * Enter description here...
 *
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class Bee_Context_AliasRegistry {
	
	/**
	 * Enter description here...
	 *
	 * @var array
	 */
	private $aliasMap = array();

	
	/**
	 * Enter description here...
	 *
	 * @param String $name
	 * @param String $alias
	 * @return void
	 */
	public function registerAlias($name, $alias) {
		Bee_Utils_Assert::hasText($name, 'name must not be empty');
		Bee_Utils_Assert::hasText($alias, 'alias must not be empty');
		if ($alias === $name) {
			unset($this->aliasMap[$alias]);
		} else {
			if (!$this->allowAliasOverriding()) {
				$registeredName = $this->aliasMap[$alias];
				if (Bee_Utils_Strings::hasText($registeredName) && $registeredName !== $name) {
					throw new Bee_Context_BeanDefinitionStoreException("Cannot register alias '$alias' for name '$name': It is already registered for name '$registeredName'.");
				}
			}
			$this->aliasMap[$alias] = $name;
		}
	}

	
	/**
	 * Return whether alias overriding is allowed.
	 * Default is <code>true</code>.
	 * 
	 * @return boolean
	 */
	protected function allowAliasOverriding() {
		// @todo: parametrize this
		return true;
	}	

	
	/**
	 * Enter description here...
	 *
	 * @param String $alias
	 * @return void
	 */
	public function removeAlias($alias) {
		if ($this->isAlias($alias)) {
			throw new Bee_Context_BeanDefinitionStoreException("No alias '$alias' registered, cannot remove it.");
		}
		unset($this->aliasMap[$alias]);
	}
	

	/**
	 * Enter description here...
	 *
	 * @param String $name
	 * @return boolean
	 */
	public function isAlias($name) {
		return array_key_exists($name, $this->aliasMap);
	}

	
	/**
	 * Enter description here...
	 *
	 * @param String $name
	 * @return array
	 */
	public function getAliases($name) {
		return array_keys($this->aliasMap, $name); 
	}

	/**
	 * Determine the raw name, resolving aliases to canonical names.
	 *
	 * @param String $name
	 * @return String
	 */
	public function canonicalName($name) {
		$canonicalName = $name;
		// Handle aliasing.
		$resolvedName = null;
		do {
			$resolvedName = $this->aliasMap[$canonicalName];
			if (!is_null($resolvedName)) {
				$canonicalName = $resolvedName;
			}
		} while (!is_null($resolvedName));
		return $canonicalName;
	}
	
	public function getAliasesFromRegistry(Bee_Context_AliasRegistry $registry) {
		$this->aliasMap = $registry->aliasMap;
	}
}
?>