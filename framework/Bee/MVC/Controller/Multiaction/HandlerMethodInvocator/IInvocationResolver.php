<?php
namespace Bee\MVC\Controller\Multiaction\HandlerMethodInvocator;
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
use Bee\MVC\IHttpRequest;

/**
 * Interface IInvocationResolver
 * @package Bee\MVC\Controller\Multiaction\HandlerMethodInvocator
 */
interface IInvocationResolver {
	/**
	 * @param IHttpRequest $request
	 * @return MethodInvocation
	 */
	public function getInvocationDefinition(IHttpRequest $request);
} 