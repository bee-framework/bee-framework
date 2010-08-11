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
 * Date: Mar 16, 2010
 * Time: 11:34:15 PM
 */

interface Bee_Security_Acls_Pdo_ILookupStrategy {

    /**
     * Perform database-specific optimized lookup.
     *
     * @param Bee_Security_Acls_IObjectIdentity[] $objects the identities to lookup (required)
     * @param Bee_Security_Acls_ISid[] $sids the SIDs for which identities are required (may be <tt>null</tt> - implementations may elect not
     *        to provide SID optimisations)
     * @return Bee_Security_Acls_IAcl[] associative array where keys represent the {@link Bee_Security_Acls_IObjectIdentity}
     *         (in terms of $oid->getIdentifierString()) of the located {@link Bee_Security_Acls_IAcl} and values are the
     *         located {@link Acl} (never <tt>null</tt> although some entries may be missing; this method should not throw
     *         {@link Bee_Security_Acls_Exception_NotFound}, as a chain of {@link Bee_Security_Acls_Pdo_ILookupStrategy}s
     *         may be used to automatically create entries if required)
     */
    public function readAclsByOidsAndSids($objects, $sids);
}
