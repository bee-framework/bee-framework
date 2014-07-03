<?php
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
use Bee\MVC\IViewResolver;

/**
 * Basic implementation of the IViewResolver interface. Uses a Bee_IContext for view name resolution, looking up
 * beans of type IView by using the view name as bean name.
 *
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class Bee_MVC_ViewResolver_Basic implements IViewResolver {

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
	 * @var Bee_IContext
	 */
	private $context;

	/**
	 * @var string
	 */
	private $ajaxViewNameSuffix = '.ajax';

	/**
	 * Enter description here...
	 *
	 * @param Bee_IContext $context
	 * @return void
	 */
	public function setContext(Bee_IContext $context) {
		$this->context = $context;
	}

	/**
	 * Enter description here...
	 *
	 * @return Bee_IContext
	 */
	public function getContext() {
		return $this->context;
	}

	/**
	 * @param String $viewName
	 * @param Bee_MVC_IHttpRequest $request
	 * @return Bee\MVC\IView|Object
	 */
	public function resolveViewName($viewName, Bee_MVC_IHttpRequest $request) {
		$modifiedViewName = $this->modifyViewName($viewName, $request);
		if ($modifiedViewName != $viewName) {
			try {
				return $this->getViewForName($viewName);
			} catch (Bee_Context_BeansException $bex) {
				if($this->getLog()->isDebugEnabled()) {
					$this->getLog()->debug('Modified view name "' . $modifiedViewName . '" not found, trying base bean name "' . $viewName . '"', $bex);
				}
			}
		}
		return $this->getViewForName($viewName);
	}

	/**
	 * @param string $viewName
	 * @return Bee\MVC\IView
	 */
	protected function getViewForName($viewName) {
		return $this->context->getBean($viewName, 'Bee\MVC\IView');
	}

	/**
	 * Modify the view name according to request specifics. By default, the suffix '.ajax' is appended to the view names
	 * for AJAX requests. Otherwise, the view name is left untouched.
	 * @param $viewName
	 * @param Bee_MVC_IHttpRequest $request
	 * @return string
	 */
	protected function modifyViewName($viewName, Bee_MVC_IHttpRequest $request) {
		return $request->getAjax() ? $viewName . $this->ajaxViewNameSuffix : $viewName;
	}
}