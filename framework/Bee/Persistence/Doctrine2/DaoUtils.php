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
use UnexpectedValueException;

/**
 * User: mp
 * Date: 02.09.13
 * Time: 20:42
 */
 
class DaoUtils {

	/**
	 * @param array $ids
	 * @return string
	 */
	public static function buildScalarId(array $ids) {
		return implode(':', $ids);
	}

	/**
	 * @param $scalarId
	 * @param array $idKeys
	 * @return array
	 * @throws \UnexpectedValueException
	 */
	public static function explodeScalarId($scalarId, array $idKeys) {
		$res = explode(':', $scalarId);
		if(!count($res) === count($idKeys)) {
			throw new UnexpectedValueException('Scalar ID representation "'. $scalarId .'" malformed, must represent values for ' . count($idKeys) . ' fields.');
		}
		return array_combine($idKeys, $res);
	}
}
