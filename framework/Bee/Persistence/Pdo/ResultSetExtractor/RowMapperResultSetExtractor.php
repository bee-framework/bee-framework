<?php
namespace Bee\Persistence\Pdo\ResultSetExtractor;

/*
 * Copyright 2008-2015 the original author or authors.
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
use PDOStatement;

/**
 * Class RowMapper
 * @package Bee\Persistence\Pdo\ResultSetExtractor
 */
class RowMapperResultSetExtractor {

    /**
     * @var callable
     */
    private $rowMapper;

    /**
     * @param callable $rowMapper
     */
    public function __construct(callable $rowMapper) {
        $this->rowMapper = $rowMapper;
    }

    public function __invoke(PDOStatement $rs) {
        $results = array();
        $rowNum = 0;
        $rm = $this->rowMapper;
        while ($result = $rm($rs, $rowNum++)) {
            $results[] = $result;
        }
        return $results;
    }
}
