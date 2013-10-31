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
 * Created by IntelliJ IDEA.
 * User: mp
 * Date: Feb 18, 2010
 * Time: 2:03:08 AM
 * To change this template use File | Settings | File Templates.
 */

class Bee_Transactions_Interceptor_NoRollbackRuleAttribute extends Bee_Transactions_Interceptor_RollbackRuleAttribute {
    public function __construct($className) {
        parent::__construct($className);
    }

    public function __toString() {
        return "No" + parent::__toString();
    }
}
?>