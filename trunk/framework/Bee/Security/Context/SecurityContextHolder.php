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

use Bee\Security\Context;

/**
 * Class SecurityContextHolder
 * @package Bee\Security\Context
 */
class SecurityContextHolder {
	
	/**
	 * Single context instance
	 *
	 * @var Context
	 */
	private static $context;
	
	/**
	 * Explicitly clears the context value from the current thread.
	 *
	 * @return void
	 */
	public static function clearContext() {
		self::$context = null;
	}
	
	/**
	 * Obtain the current <code>SecurityContext</code>.
	 *
	 * @return Context the security context (never <code>null</code>)
	 */
	public static function getContext() {
		if(is_null(self::$context)) {
			self::$context = new Context();
		}
		return self::$context;
	}
	
	/**
	 * Associates a new <code>SecurityContext</code> with the current thread of execution.
	 *
	 * @param Context $context the new <code>SecurityContext</code> (may not be <code>null</code>)
	 */
	public static function setContext(Context $context) {
		self::$context = $context;
	}
}