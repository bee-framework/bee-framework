<?php
namespace Bee\Persistence\Pdo;
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

/**
 * User: mp
 * Date: 07.05.13
 * Time: 14:42
 */

class FeatureDetector {

	const FEATURE_ORDERED_UPDATE = 'ORDERED_UPDATE';

	private static $FEATURE_CACHE = array();

	private static $DETECTORS;

	/**
	 * @var \Logger
	 */
	private static $log;

	/**
	 * @return \Logger
	 */
	protected static function getLog() {
		if (!self::$log) {
			self::$log = \Bee_Framework::getLoggerForClass(__CLASS__);
		}
		return self::$log;
	}

	/**
	 * @param string $feature
	 * @param \PDO $pdoConnection
	 * @return bool
	 */
	public static function supports($feature, \PDO $pdoConnection) {
		self::init();
		$connKey = spl_object_hash($pdoConnection);
		if (!array_key_exists($connKey, self::$FEATURE_CACHE)) {
			self::$FEATURE_CACHE[$connKey] = array();
		}
		if (!array_key_exists($feature, self::$FEATURE_CACHE[$connKey])) {
			$detector = self::$DETECTORS[$feature];
			$result = $detector($pdoConnection);
			self::$FEATURE_CACHE[$connKey][$feature] = $result;
			self::getLog()->info("PDO connection $connKey supports feature $feature : " . ($result ? "YES" : "NO"));
		}
		return self::$FEATURE_CACHE[$connKey][$feature];
	}

	private static function init() {
		if (!self::$DETECTORS) {
			self::$DETECTORS = array(
				self::FEATURE_ORDERED_UPDATE => function (\PDO $pdoConnection) {
					switch ($pdoConnection->getAttribute(\PDO::ATTR_DRIVER_NAME)) {
						case 'mysql':
							return true;
					}
					return false;
				}
			);
		}
	}
}

