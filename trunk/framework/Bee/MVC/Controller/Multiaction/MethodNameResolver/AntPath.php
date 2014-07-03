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
use Bee\MVC\Controller\Multiaction\MethodNameResolver\AntPathMethodNameResolver;

/**
 * A method name resolver that uses a mapping from ant-style path definitions to handler method names. The path definitions are parsed
 * by <code>Bee_Utils_AntPathMatcher</code> and can contain wildcards (*, ?).
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 * @deprecated replaced by Bee\MVC\Controller\Multiaction\MethodNameResolver\AntPathMethodNameResolver
 */
class Bee_MVC_Controller_Multiaction_MethodNameResolver_AntPath extends AntPathMethodNameResolver {
}
