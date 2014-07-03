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
use Bee\MVC\IController;
use Bee\MVC\IHandlerExceptionResolver;

class Bee_MVC_SimpleMappingExceptionResolver implements IHandlerExceptionResolver {

	const MODEL_HANDLER_EXCEPTION_KEY = 'handler_excpetion';

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
	 *
	 * @var array
	 */
	private $exceptionMapping;

	/**
	 *
	 * @var String
	 */
	private $defaultErrorView;

	/**
	 *
	 * @return array
	 */
	public final function getExceptionMapping() {
		return $this->exceptionMapping;
	}

	/**
	 *
	 * @param array $exceptionMapping
	 * @return void
	 */
	public final function setExceptionMapping(array $exceptionMapping) {
		$this->exceptionMapping = $exceptionMapping;
	}

	/**
	 *
	 * @return String
	 */
	public final function getDefaultErrorView() {
		return $this->defaultErrorView;
	}

	/**
	 *
	 * @param String $defaultErrorView
	 * @return void
	 */
	public final function setDefaultErrorView($defaultErrorView) {
		$this->defaultErrorView = $defaultErrorView;
	}

	/**
	 * @param Bee_MVC_IHttpRequest $request
	 * @param IController $handler
	 * @param Exception $ex
	 * @return Bee_MVC_ModelAndView|bool
	 */
	public function resolveException(Bee_MVC_IHttpRequest $request, IController $handler = null, Exception $ex) {
		$this->getLog()->info('Trying to map exception', $ex);

		$viewName = false;

		// can the mapping be resolved directly?
		if (is_array($this->exceptionMapping)) {
			$exceptionClass = get_class($ex);
			// can the mapping be resolved directly?
			if (array_key_exists($exceptionClass, $this->exceptionMapping)) {
				$viewName = $this->exceptionMapping[$exceptionClass];
			} else {
				// try to find a mapping for a superclass
				foreach ($this->exceptionMapping as $parentClass => $mappedSolution) {
					if (is_subclass_of($exceptionClass, $parentClass)) {
						$viewName = $mappedSolution;
						break;
					}
				}
			}
		}

		if (!$viewName && Bee_Utils_Strings::hasText($this->defaultErrorView)) {
			$viewName = $this->defaultErrorView;
			$this->getLog()->debug('Resolving to default error view');
		}

		$this->getLog()->debug('Resolved error view: ' . $viewName);
		return $viewName ? new Bee_MVC_ModelAndView(array(self::MODEL_HANDLER_EXCEPTION_KEY => $ex), $viewName) : false;
	}
}
