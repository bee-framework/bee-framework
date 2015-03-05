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

interface IHandler {
	
	/**
	 * Enter description here...
	 *
	 * @param String $sessionId
	 * @return String the serialized session data
	 * 
	 * @throws
	 */
	public function read($sessionId);
	
	/**
	 * Enter description here...
	 *
	 * @param String $sessionId
	 * @param String $sessionData the serialized session data
	 * @return void
	 * 
	 * @throws
	 */
	public function write($sessionId, $sessionData);
	
	/**
	 * Enter description here...
	 *
	 * @param String $sessionId
	 * @return void
	 * 
	 * @throws
	 */
	public function destroy($sessionId);
	
	/**
	 * Enter description here...
	 *
	 * @param integer $ttl
	 * @return void
	 * 
	 * @throws
	 */
	public function gc($ttl);
}