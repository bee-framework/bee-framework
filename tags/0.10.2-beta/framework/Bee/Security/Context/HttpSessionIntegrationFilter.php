<?php
namespace Bee\Security\Context;
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

use Bee\MVC\IFilter;
use Bee\MVC\IFilterChain;
use Bee\MVC\IHttpRequest;

/**
 * Class HttpSessionIntegrationFilter
 * @package Bee\Security\Context
 */
class HttpSessionIntegrationFilter implements IFilter {
	
	const BEE_SECURITY_CONTEXT_KEY = 'BEE_SECURITY_CONTEXT';
	
	/**
	 * Enter description here...
	 *
	 * @var mixed
	 */
	private $contextObject;
	
	/**
	 * Enter description here...
	 *
	 * @var bool
	 */
    private $allowSessionCreation = true;
    
    /**
     * Enter description here...
     *
     * @var boolean
     */
    private $forceEagerSessionCreation = false;
    
    public function setDummy($dummy) {
    }
    
    public function __construct() {
    }
    
	public function doFilter(IHttpRequest $request, IFilterChain $filterChain) {
		
		// lazily start the session if we do have a SID
//		if(session_id === '') {
//			$sessParamName = ini_get('session.name');
//			if(array_key_exists($sessParamName, $_REQUEST)) {
				session_start();
//			}
//		}
		
		if(array_key_exists(self::BEE_SECURITY_CONTEXT_KEY, $_SESSION)) {
			SecurityContextHolder::setContext($_SESSION[self::BEE_SECURITY_CONTEXT_KEY]);
		}

		$filterChain->doFilter($request);
		
		// @todo: detect change in SecurityContext, only create a session if such change ocurred
		// spl_object_hash _could_ help, but is not a solution by itself

		$_SESSION[self::BEE_SECURITY_CONTEXT_KEY] = SecurityContextHolder::getContext();
	}
}
