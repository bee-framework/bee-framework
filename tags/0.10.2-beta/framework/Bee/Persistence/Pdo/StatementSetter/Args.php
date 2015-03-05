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
use Bee\Utils\Assert;

/**
 * User: mp
 * Date: Mar 20, 2010
 * Time: 1:17:41 AM
 *
 * TODO: derive type information for args and use appropriate bindValue(*, *, PDO::PARAM_*) type info!!
 */
class Bee_Persistence_Pdo_StatementSetter_Args implements Bee_Persistence_Pdo_IStatementSetter {

    /**
     * @var array
     */
    private $args;

	/**
	 * @var array
	 */
	private $types;

	/**
	 * @param array $args
	 * @param array $types
	 * @return \Bee_Persistence_Pdo_StatementSetter_Args
	 */
    public function __construct(array $args, array $types = null) {
		$this->args = $args;
		if (is_array($types)) {
			Assert::isTrue(count($args) == count($types), 'Size of types array must be equal to size of args array');
			$this->types = $types;
		}
    }

    public function setValues(PDOStatement $ps) {
        if (!is_null($this->args)) {
            for ($i = 0; $i < count($this->args); $i++) {
                $arg = $this->args[$i];

                if (is_array($this->types)) {
                    $dataType = $this->types[$i];

                } else {
                    if (is_int($arg) && is_bool($arg)) {
                        $dataType = PDO::PARAM_INT;

                    } else if (is_null($arg)) {
                        $dataType = PDO::PARAM_NULL;
                        
                    } else {
                        $dataType = PDO::PARAM_STR;
                    }
                }

                if (!$ps->bindValue($i + 1, $arg, $dataType)) {
                    throw new Exception('PDO Statement failed. bindValue returned false for: "'.$arg.'" of type '.gettype($arg));
                }
            }
        }

    }

}

