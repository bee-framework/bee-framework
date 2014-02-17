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
 * @author Michael Plomer
 */
class Bee_Beans_PropertyEditor_Generic extends Bee_Beans_PropertyEditor_Abstract {

	private $filterId;

	private $filterFlags;

	private $filterName = false;

	public function __construct($filterId, $filterFlags = 0) {
		$this->filterId = $filterId;
		$this->filterFlags = $filterFlags;
	}

	/**
	 * Enter description here...
	 *
	 * @param String $value
	 * @return int
	 */
	public function fromString($value) {
		return $this->checkAndReturnIfNotFalse(filter_var($value, $this->filterId, $this->filterFlags), $value);
	}

	protected function getClassOrTypeName() {
		$this->initFilterName();
		return parent::getClassOrTypeName().'['.$this->filterName.']';
	}

	private function initFilterName() {
		if(!$this->filterName) {
			$filters = filter_list();
			foreach($filters as $filter_name) {
				if($this->filterId == filter_id($filter_name)) {
					$this->filterName = $filter_name;
				}
			}
		}
	}
}