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
use Bee\MVC\Controller\Multiaction\MethodNameResolver\ParameterMethodNameResolver;

/**
 * Method name resolver implementation that uses the value of a request parameter ('action' by default) as the name of the handler method.
 *
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 *
 * @deprecated replaced by Bee\MVC\Controller\Multiaction\MethodNameResolver\ParameterMethodNameResolver
 */
class Bee_MVC_Controller_Multiaction_MethodNameResolver_Parameter extends ParameterMethodNameResolver {
}