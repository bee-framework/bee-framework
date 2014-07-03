<?php
namespace Bee\MVC\Controller\Multiaction;
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
use Bee\MVC\Controller\MultiActionController;
use Logger;

/**
 * Class AbstractControllerHolder
 */
abstract class AbstractControllerHolder {

	/**
	 * @var Logger
	 */
	protected $log;

	/**
	 * @return Logger
	 */
	protected function getLog() {
		if (!$this->log) {
			$this->log = Logger::getLogger(get_class($this));
		}
		return $this->log;
	}

	/**
	 * Enter description here...
	 *
	 * @var MultiActionController
	 */
	private $controller;

	public function setController(MultiActionController $controller) {
		$this->controller = $controller;
	}

	/**
	 * Enter description here...
	 *
	 * @return MultiActionController
	 */
	public function getController() {
		return $this->controller;
	}
}