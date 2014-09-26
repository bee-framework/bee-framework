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
use Bee\MVC\ViewResolver\BasicViewResolver;

/**
 * Basic implementation of the IViewResolver interface. Uses a Bee_IContext for view name resolution, looking up
 * beans of type IView by using the view name as bean name.
 *
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 * @deprecated replaced by BasicViewResolver
 */
class Bee_MVC_ViewResolver_Basic extends BasicViewResolver {
}