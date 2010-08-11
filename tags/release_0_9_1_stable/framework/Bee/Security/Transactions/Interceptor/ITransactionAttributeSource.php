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
 * Time: 2:36:33 AM
 * To change this template use File | Settings | File Templates.
 */

interface Bee_Transactions_Interceptor_ITransactionAttributeSource {

    /**
     * Return the transaction attribute for this method.
     * Return null if the method is non-transactional.
     * @param ReflectionMethod $method method
     * @param string $targetClassName target class name. May be <code>null</code>, in which case the declaring class of
     * the method must be used.
     * @return Bee_Transactions_Interceptor_ITransactionAttribute the matching transaction attribute,
     * or <code>null</code> if none found
     */
    function getTransactionAttribute(ReflectionMethod $method, $targetClassName);
}
