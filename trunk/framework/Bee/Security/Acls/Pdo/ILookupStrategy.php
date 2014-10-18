<?php
namespace Bee\Security\Acls\Pdo;
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
use Bee\Security\Acls\IAcl;
use Bee\Security\Acls\IObjectIdentity;
use Bee\Security\Acls\ISid;

/**
 * User: mp
 * Date: Mar 16, 2010
 * Time: 11:34:15 PM
 */

interface ILookupStrategy {

    /**
     * Perform database-specific optimized lookup.
     *
     * @param IObjectIdentity[] $objects the identities to lookup (required)
     * @param ISid[] $sids the SIDs for which identities are required (may be <tt>null</tt> - implementations may elect not
     *        to provide SID optimisations)
     * @return IAcl[] associative array where keys represent the {@link IObjectIdentity}
     *         (in terms of $oid->getIdentifierString()) of the located {@link IAcl} and values are the
     *         located {@link Acl} (never <tt>null</tt> although some entries may be missing; this method should not throw
     *         {@link NotFoundException}, as a chain of {@link ILookupStrategy}s
     *         may be used to automatically create entries if required)
     */
    public function readAclsByOidsAndSids($objects, $sids);
}
