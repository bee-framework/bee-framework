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
use Bee\MVC\Controller\MultiActionController;

/**
 * The multiaction controller handles requests by delegating to method calls on a delegate object. The methods to be used are
 * determined by the method name resolver.
 *
 * A handler method must take at least one argument, type-hinted to Bee_MVC_IHttpRequest, and return a Bee_MVC_ModelAndView.
 *
 * @see Bee_MVC_IHttpRequest
 * @see Bee_MVC_ModelAndView
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 * @author Benjamin Hartmann
 *
 * @deprecated replaced by \Bee\MVC\Controller\MultiActionController
 */
class Bee_MVC_Controller_MultiAction extends MultiActionController {
}
