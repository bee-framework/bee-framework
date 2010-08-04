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

/**
 * User: mp
 * Date: Mar 20, 2010
 * Time: 1:17:41 AM
 */

class Bee_Persistence_Pdo_StatementSetter_Args implements Bee_Persistence_Pdo_IStatementSetter {

    /**
     * @var array
     */
    private $args;

    /**
     * @param  $args
     * @return void
     */
    public function __construct(array $args) {
        $this->args = $args;
    }

    public function setValues(PDOStatement $ps) {
        if (!is_null($this->args)) {
            for ($i = 0; $i < count($this->args); $i++) {
                $arg = $this->args[$i];
                $ps->bindValue($i + 1, $arg);
            }
        }

    }

}
