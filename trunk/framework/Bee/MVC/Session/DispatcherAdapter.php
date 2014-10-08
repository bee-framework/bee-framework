<?php
namespace Bee\MVC\Session;
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

use Bee\IContext;
use Exception;

class DispatcherAdapter {
	
	const SESSION_HANDLER_NAME = '__sessionHandler';

	/**
	 * Enter description here...
	 *
	 * @var IContext
	 */
	private $context;
	
	/**
	 * Enter description here...
	 *
	 * @var IHandler
	 */
	private $handler;

	/**
	 * @param IContext $context
	 */
	public function __construct(IContext $context) {
		$this->context = $context;
	}
	
	public final function open($save_path, $session_name) {
		return true;
	}

	public final function close() {
		return true;
	}

	public final function read($sessionId) {
		try {
			$this->fetchHandler();
			return $this->handler->read($sessionId);			
		} catch (Exception $e) {
			// @todo: Log (preferably to file)
			return false;
		}
	}

	public final function write($sessionId, $sessionData) {
		try {
			$this->fetchHandler();
			$this->handler->write($sessionId, $sessionData);
			return true;			
		} catch (Exception $e) {
			// @todo: Log (preferably to file)
			return false;
		}
	}

	public final function destroy($sessionId) {
		try {
			$this->fetchHandler();
			$this->handler->destroy($sessionId);
			return true;			
		} catch (Exception $e) {
			// @todo: Log (preferably to file)
			return false;
		}
	}
	
	public final function gc($ttl) {
		try {
			$this->fetchHandler();
			$this->handler->gc($ttl);
			return true;			
		} catch (Exception $e) {
			// @todo: Log (preferably to file)
			return false;
		}
	}
	
	private function fetchHandler() {
		if(is_null($this->handler)) {
			$this->handler = $this->context->getBean(self::SESSION_HANDLER_NAME, 'Bee\MVC\Session\IHandler');
		}		
	}
}