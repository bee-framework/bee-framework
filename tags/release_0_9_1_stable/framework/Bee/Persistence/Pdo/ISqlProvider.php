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
 * Date: Mar 17, 2010
 * Time: 10:31:56 AM
 */

interface Bee_Persistence_Pdo_ISqlProvider {

    /**
     * Return the SQL string for this object, i.e.
     * typically the SQL used for creating statements.
     * @return string the SQL string, or <code>null</code>
     */
    function getSql();
}
