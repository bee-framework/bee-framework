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
 * Time: 18:24
 */
 
class Utils {

	/**
	 * @param \PDOStatement $qry
	 * @param array $params
	 * @return mixed
	 */
	public static function fetchOne(\PDOStatement $qry, array $params) {
		$qry->execute($params);
		$res = $qry->fetch(\PDO::FETCH_NUM);
		return $res [0];
	}

	/**
	 * @param \PDOStatement $qry
	 * @param array $params
	 * @return array
	 */
	public static function fetchRow(\PDOStatement $qry, array $params) {
		$qry->execute($params);
		return $qry->fetch(\PDO::FETCH_ASSOC);
	}
}
