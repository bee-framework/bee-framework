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
 * Date: Mar 18, 2010
 * Time: 12:51:38 AM
 */

class Bee_Security_Acls_Impl_BasicAuditLogger implements Bee_Security_Acls_IAuditLogger {
    public function logIfNeeded($granted, Bee_Security_Acls_IAccessControlEntry $ace) {
        Bee_Utils_Assert::notNull($ace, 'AccessControlEntry required');

        if ($ace instanceof Bee_Security_Acls_IAuditableAccessControlEntry) {
            if ($granted && $ace->isAuditSuccess()) {
                Bee_Utils_Logger::info('GRANTED due to ACE: ' . $ace->getId());
            } else if (!$granted && $auditableAce->isAuditFailure()) {
                Bee_Utils_Logger::info('DENIED due to ACE: ' . $ace->getId());
            }
        }
    }
}
