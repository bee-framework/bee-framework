<?php
namespace Bee\MVC\ViewResolver;
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
use Bee\Context\BeansException;
use Bee\IContext;
use Bee\MVC\IHttpRequest;
use Bee\MVC\IView;
use Bee\MVC\IViewResolver;
use Bee\MVC\ModelAndView;
use Bee\MVC\View\ViewBase;
use Logger;

/**
 * Basic implementation of the IViewResolver interface. Uses a Bee\IContext for view name resolution, looking up
 * beans of type IView by using the view name as bean name.
 *
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class BasicViewResolver implements IViewResolver {

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
	 * @var IContext
	 */
	private $context;

	/**
	 * @var string
	 */
	private $ajaxViewNameSuffix = '.ajax';

	/**
	 * Enter description here...
	 *
	 * @param IContext $context
	 * @return void
	 */
	public function setContext(IContext $context) {
		$this->context = $context;
	}

	/**
	 * Enter description here...
	 *
	 * @return IContext
	 */
	public function getContext() {
		return $this->context;
	}

	/**
	 * @param String $viewName
	 * @param IHttpRequest $request
	 * @return IView|Object
	 */
	public function resolveViewName($viewName, IHttpRequest $request) {
		$modifiedViewName = $this->modifyViewName($viewName, $request);
		if ($modifiedViewName != $viewName) {
			try {
				return $this->getViewForName($modifiedViewName);
			} catch (BeansException $bex) {
				if($this->getLog()->isDebugEnabled()) {
					$this->getLog()->debug('Modified view name "' . $modifiedViewName . '" not found, trying base bean name "' . $viewName . '"', $bex);
				}
			}
		}
		return $this->getViewForName($viewName);
	}

	/**
	 * @param string $viewName
	 * @return IView
	 */
	protected function getViewForName($viewName) {
		return $this->context->getBean($viewName, 'Bee\MVC\IView');
	}

	/**
	 * Modify the view name according to request specifics. By default, the suffix '.ajax' is appended to the view names
	 * for AJAX requests. Otherwise, the view name is left untouched.
	 * @param $viewName
	 * @param IHttpRequest $request
	 * @return string
	 */
	protected function modifyViewName($viewName, IHttpRequest $request) {
		return $request->getAjax() ? $viewName . $this->ajaxViewNameSuffix : $viewName;
	}

	/**
	 * @param ModelAndView $mav
	 * @param IHttpRequest $request
	 */
	public function resolveModelAndView(ModelAndView $mav, IHttpRequest $request) {
		$resolvedView = $this->resolveViewName($mav->getViewName(), $request);
		$mav->setResolvedView($resolvedView);
		if ($resolvedView instanceof ViewBase) {
			$statics = $resolvedView->getStaticAttributes();
			if (!$statics) {
				$statics = array();
			}
			$model = array_merge($statics, $mav->getModel());
			$mav->setModel($model);
		}
		$this->resolveModelInternals($mav->getModel(), $request);
	}

	/**
	 * @param array $model
	 * @param IHttpRequest $request
	 */
	private function resolveModelInternals(array $model, IHttpRequest $request) {
		foreach ($model as $modelElem) {
			if ($modelElem instanceof ModelAndView) {
				$this->resolveModelAndView($modelElem, $request);
			} else if (is_array($modelElem)) {
				$this->resolveModelInternals($modelElem, $request);
			}
		}
	}
}