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
use Bee\MVC\Controller\Multiaction\MethodNameResolver\SimplePathMethodNameResolver;

/**
 * A very simple method name resolver implementation that uses the last fragment of the path info as the method name.
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 * @author Benjamin Hartmann
 *
 * @deprecated replaced by Bee\MVC\Controller\Multiaction\MethodNameResolver\SimplePathMethodNameResolver
 */
class Bee_MVC_Controller_Multiaction_MethodNameResolver_SimplePath extends SimplePathMethodNameResolver {
}