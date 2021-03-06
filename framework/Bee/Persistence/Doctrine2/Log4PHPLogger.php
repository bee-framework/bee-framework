<?php
namespace Bee\Persistence\Doctrine2;
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
use Bee\Framework;
use Bee\Utils\TLogged;
use Doctrine\DBAL\Logging\SQLLogger;

/**
 * User: mp
 * Date: 21.06.13
 * Time: 16:23
 *
 * Implementation of the Doctrine 2 SQLLogger interface that logs to Log4PHP
 */
class Log4PHPLogger implements SQLLogger {
    use TLogged;

	/**
	 * @var int
	 */
	private $startTime;

	/**
	 * Logs a SQL statement somewhere.
	 *
	 * @param string $sql The SQL to be executed.
	 * @param array $params The SQL parameters.
	 * @param array $types The SQL parameter types.
	 * @return void
	 */
	public function startQuery($sql, array $params = null, array $types = null) {
		$this->getLog()->trace('SQL : [' . $sql . '] PARAMS : [' . var_export($params, true) . '] TYPES: ['. var_export($types, true) . ']');
		$this->startTime = microtime(true);
	}

	/**
	 * Mark the last started query as stopped. This can be used for timing of queries.
	 *
	 * @return void
	 */
	public function stopQuery() {
		$dur = microtime(true) - $this->startTime;
		$this->getLog()->trace('.. completed in '. ($dur*1000) . ' ms');
	}
}
