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


class Bee_Filesystem_MimeDetector {
	
	private static $types;
	
	private static function parseMimeTypeFile() {
		
	}
	
	/**
	 * Enter description here...
	 *
	 * @param String $filename
	 * @return String
	 */
	public static function detect($filename) {
		if(is_null(self::$types)) {
			self::$types = Bee_Cache_Manager::retrieveCachable(new Bee_Filesystem_MimedictionaryCacheable());
		}
		
		$extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
		
		if(array_key_exists($extension, self::$types)) {
			return self::$types[$extension];
		}

		return false;
	}
	
	public static function match($filename, $mimeType) {
		 
	}
}

class Bee_Filesystem_MimedictionaryCacheable implements Bee_Cache_ICachableResource {
	
	const MIME_DICTIONARY_FILE = 'mime.types';
	
	public function getKey() {
		return '__Bee_Filesystem_MimeDetector_dictionary';
	}
	
	public function getModificationTimestamp() {
		return filemtime(dirname(__FILE__) . DIRECTORY_SEPARATOR . self::MIME_DICTIONARY_FILE);
	}
	
	public function &createContent(&$expirationTimestamp = 0) {
		$result = array();

		$dict = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . self::MIME_DICTIONARY_FILE);		
		$dict = Bee_Utils_Strings::tokenizeToArray($dict, "\n\r");
		
		foreach($dict as $line) {
			if(Bee_Utils_Strings::hasText($line) && !Bee_Utils_Strings::startsWith($line, '#')) {
				$line = Bee_Utils_Strings::tokenizeToArray($line, " \t");
				$count = count($line);
				if($count > 1) {
					for($i = 1; $i < $count; $i++) {
						$result[$line[$i]] = $line[0];
					}
				}
			}
		}
		
		return $result;
	}
}
?>