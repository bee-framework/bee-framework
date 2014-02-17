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
 * Basic implementation of the Bee_MVC_IViewResolver interface. Uses a Bee_IContext for view name resolution, looking up
 * beans of type Bee_MVC_IView by using the view name as bean name.
 * 
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class Bee_MVC_ViewResolver_Basic implements Bee_MVC_IViewResolver {

	/**
	 * Enter description here...
	 *
	 * @var Bee_IContext
	 */
	private $context;
	
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
	
	public function resolveViewName($viewName) {
		return $this->context->getBean($viewName, 'Bee_MVC_IView');
	}
}

?>