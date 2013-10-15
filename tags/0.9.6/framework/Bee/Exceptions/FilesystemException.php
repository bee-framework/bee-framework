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

class Bee_Exceptions_FilesystemException extends Bee_Exceptions_Base {
	
	const MESSAGE_UNKNOWN_ERROR = 'filesystem.unknown.error';
	const MESSAGE_RESOURCE_EXISTS = 'filesystem.resource.exists';
	const MESSAGE_RESOURCE_DOES_NOT_EXIST = 'filesystem.resource.doesnotexist';
	const MESSAGE_COULD_NOT_CREATE = 'filesystem.couldnotcreate';
	const MESSAGE_COULD_NOT_MOVE = 'filesystem.couldnotmove';
	const MESSAGE_COULD_NOT_COPY = 'filesystem.couldnotcopy';
	const MESSAGE_COULD_NOT_RENAME = 'filesystem.couldnotrename';
	const MESSAGE_COULD_NOT_DELETE = 'filesystem.couldnotdelete';
	
	private $path;
	
	public function __construct($message, $path = null, Exception $cause = null) {
		parent::__construct($message, $cause);
		$this->path = $path;
	}
	
	public final function getPath() {
		return $this->path;
	}
}
?>