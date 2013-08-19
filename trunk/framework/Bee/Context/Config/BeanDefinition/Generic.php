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
use Bee\Beans\MethodInvocation;

/**
 * Enter description here...
 *
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class Bee_Context_Config_BeanDefinition_Generic extends Bee_Context_Config_BeanDefinition_Abstract {
	
	private $parentName;
	
	public function __construct(Bee_Context_Config_IBeanDefinition $original = null) {
		parent::__construct($original);
	}
	
	
	public function getParentName() {
		return $this->parentName;
	}
	
	
	public function setParentName($parentName) {
		$this->parentName = $parentName;
	}

	/**
     *
     * @param  $id
     * @return void
     */
    // todo: this isn't actually a toString()... prints lots of stuff itself...
	public function toString($id=null) {
		echo '<b>Bean Definition:</b><br/>';
		if (!is_null($id)) {
			echo 'id: '.$id.'<br/>';
		}
		echo 'class: '.$this->getBeanClassName().'<br/>';
		echo 'Scope: '.$this->getScope().'<br/>';
		$args = $this->getConstructorArgumentValues();
		if (!empty($args)) {
			echo '<br/>';
			echo 'constructor args:<br/>';
			foreach ($args as $value) {
				var_dump($value);
				echo '<br/>';
			}
		}
		$props = $this->getPropertyValues();
		if (!empty($props)) {
			echo '<br/>';
			echo 'properties:<br/>';
			foreach ($props as $key =>  $value) {
				echo "$key: ";
				var_dump($value);
				echo '<br/>';
			}
		}
		echo '<hr/>';
	}
}
