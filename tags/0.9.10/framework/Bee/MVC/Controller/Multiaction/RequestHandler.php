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
use Addendum\Annotation;

/**
 * Addendum annotation for the annotation-based method name resolver.
 *
 * @see Bee_MVC_Controller_Multiaction_MethodNameResolver_AnnotationBased
 * 
 * @author Michael Plomer <michael.plomer@iter8.de>
 * 
 * @Target("method")
 */
class Bee_MVC_Controller_Multiaction_RequestHandler extends Annotation {
	public $httpMethod;
	public $pathPattern;
}
?>