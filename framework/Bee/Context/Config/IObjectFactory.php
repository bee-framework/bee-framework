<?php
namespace Bee\Context\Config;
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
use Bee\Context\BeansException;

/**
 * Enter description here...
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 * @author Benjamin Hartmann
 */
interface IObjectFactory {

	/**
	 * Enter description here...
	 *
	 * @throws BeansException
	 * 
	 * @return mixed
	 */
	function getObject();

    /**
     * Return timestamp for the time the definition source of this object factory has been last modified
     * @abstract
     * @return int
     */
    function getModificationTimestamp();
}
