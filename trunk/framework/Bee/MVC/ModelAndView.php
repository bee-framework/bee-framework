<?php
namespace Bee\MVC;
/*
 * Copyright 2008-2015 the original author or authors.
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
final class ModelAndView {

	/**
	 * Enter description here...
	 *
	 * @var array
	 */
	private $model;

	/**
	 * Enter description here...
	 *
	 * @var string
	 */
	private $viewName;
	
	/**
	 * Enter description here...
	 *
	 * @var IView
	 */
	private $resolvedView;
	
	/**
	 * Enter description here...
	 *
	 * @var boolean
	 */
	private $cleared = false;
	
	/**
	 * Enter description here...
	 *
	 * @param array $model
	 * @param string $viewName
	 */
	public function __construct(array $model/* = null */, $viewName) {
		$this->model = $model;
		$this->viewName = $viewName;
	}
	
	public function addModelValue($key, $value) {
		$this->model[$key] = $value;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return array
	 */
	public function &getModel() {
		return $this->model;
	}
	
	/**
	 * 
	 * @param array $model
	 * @return void
	 */
	public function setModel(array &$model) {
		$this->model = $model;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return string
	 */
	public function getViewName() {
		return $this->viewName;
	}

	/**
	 * Enter description here...
	 *
	 * @param IView $resolvedView
	 */
	public function setResolvedView(IView $resolvedView) {
		$this->resolvedView = $resolvedView;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return IView
	 */
	public function getResolvedView() {
		return $this->resolvedView;
	}

	/**
	 * Clear the state of this ModelAndView object.
	 * The object will be empty afterwards.
	 * <p>Can be used to suppress rendering of a given ModelAndView object
	 * in the <code>postHandle</code> method of a HandlerInterceptor.
	 * @see #isEmpty()
	 * @see HandlerInterceptor#postHandle
	 */
	public function clear() {
		$this->viewName = NULL;
		$this->resolvedView = NULL;
		$this->model = array();
		$this->cleared = true;
	}

	/**
	 * Return whether this ModelAndView object is empty
	 * i.e. whether it does not hold any view and does not contain a model.
	 */
	public function isEmpty() {
		return ($this->viewName == NULL && $this->model == NULL);
	}

	/**
	 * Return whether this ModelAndView object is empty as a result of a call to {@link #clear}
	 * i.e. whether it does not hold any view and does not contain a model.
	 * Returns <code>false</code> if any additional state was added to the instance
	 * <strong>after</strong> the call to {@link #clear}.
	 * @see #clear()
	 */
	public function wasCleared() {
		return ($this->cleared && $this->isEmpty());
	}

	/**
	 * Enter description here...
	 *
	 * @return void 
	 */
	public function renderModelInView() {
		// @todo: assert a resolvedView is set
        $this->resolvedView->render($this->getModel());
	}
}