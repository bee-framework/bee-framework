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

class Bee_MVC_Session_PDOHandler implements Bee_MVC_Session_IHandler {
	
	const DEFAUlT_SESSION_TABLE_NAME = 'BEE_SESSIONS';

	/**
	 * Enter description here...
	 *
	 * @var PDO
	 */
	private $pdoConnection;
	
	/**
	 * Enter description here...
	 *
	 * @var String
	 */
	private $sessionTableName = self::DEFAUlT_SESSION_TABLE_NAME;

	/**
	 * Enter description here...
	 *
	 * @return PDO
	 */
	public final function getPdoConnection() {
		return $this->pdoConnection; 
	}

	/**
	 * Enter description here...
	 *
	 * @param PDO $pdoConnection
	 * @return void
	 */
	public final function setPdoConnection(PDO $pdoConnection) {
		$this->pdoConnection = $pdoConnection;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return String
	 */
	public final function getSessionTableName() {
		return $this->sessionTableName;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param String $sessionTableName
	 * @return void
	 */
	public final function setSessionTableName($sessionTableName) {
		$this->sessionTableName = $sessionTableName;
	}

	public function read($sessionId) {
		$stmt = $this->pdoConnection->prepare('SELECT value FROM ' . $this->sessionTableName . ' WHERE id = :sessionId');
		$stmt->execute(array(':sessionId' => $sessionId));
		$result = $stmt->fetchColumn();
		if($result !== false) {
			return $result;
		}
		throw new Exception('PDOHandler::read() : No session with ID ' . $sessionId . ' found in database');
	}
	
	public function write($sessionId, $sessionData) {
		$params = array(':sessionId' => $sessionId, ':sessionTime' => time(), ':value' => $sessionData);

		$stmt = $this->pdoConnection->prepare('UPDATE ' . $this->sessionTableName . ' SET session_time = :sessionTime, value = :value WHERE id = :sessionId');
		$stmt->execute($params);		
		if($stmt->rowCount() > 0) {
			return true;
		}
		
		$stmt = $this->pdoConnection->prepare('INSERT INTO ' . $this->sessionTableName . ' (id, session_time, session_start, value) VALUES (:sessionId, :sessionTime, :sessionTime, :value)');
		$stmt->execute($params);
		if($stmt->rowCount() > 0) {
			return true;
		}
		
		throw new Exception('PDOHandler::write() : Could not insert record for session with ID ' . $sessionId . ' ... very strange!');
	}
	
	public function destroy($sessionId) {
		$stmt = $this->pdoConnection->prepare('DELETE FROM ' . $this->sessionTableName . ' WHERE id = :sessionId');
		$stmt->execute(array(':sessionId' => $sessionId));
		if($stmt->rowCount() > 0) {
			return true;
		}
		throw new Exception('PDOHandler::destroy() : No session with ID ' . $sessionId . ' found in database');
	}
	
	public function gc($ttl) {
		$stmt = $this->pdoConnection->prepare('DELETE FROM ' . $this->sessionTableName . ' WHERE session_time < :sessionTime');
		$stmt->execute(array(':sessionTime' => time() - $ttl));
		return true;
	}
}
?>