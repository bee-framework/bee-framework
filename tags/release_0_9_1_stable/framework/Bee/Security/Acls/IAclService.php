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
 * Time: 6:49:57 PM
 */

interface Bee_Security_Acls_IAclService {

    /**
     * Locates all object identities that use the specified parent.  This is useful for administration tools.
     *
     * @abstract
     * @param Bee_Security_Acls_IObjectIdentity $parentIdentity to locate children of
     * @return Bee_Security_Acls_IObjectIdentity[] the children (or <tt>null</tt> if none were found)
     */
    public function findChildren(Bee_Security_Acls_IObjectIdentity $parentIdentity);

    /**
     * Same as {@link #readAclsForOids(ObjectIdentity[])} except it returns only a single Acl.<p>This method
     * should not be called as it does not leverage the underlaying implementation's potential ability to filter
     * <tt>Acl</tt> entries based on a {@link Sid} parameter.</p>
     *
     * @param Bee_Security_Acls_IObjectIdentity $object to locate an {@link Acl} for
     * @return Bee_Security_Acls_IAcl the {@link Acl} for the requested {@link Bee_Security_Acls_IObjectIdentity} (never <tt>null</tt>)
     * @throws Bee_Security_Acls_Exception_NotFound if an {@link Acl} was not found for the requested {@link ObjectIdentity}
     */
    public function readAclForOid(Bee_Security_Acls_IObjectIdentity $object);

    /**
     * Same as {@link #readAclsForOidsAndSids(ObjectIdentity[], Sid[])} except it returns only a single Acl.
     *
     * @param object to locate an {@link Acl} for
     * @param sids the security identities for which  {@link Acl} information is required
     *        (may be <tt>null</tt> to denote all entries)
     * @return Bee_Security_Acls_IAcl the {@link Bee_Security_Acls_IAcl} for the requested {@link Bee_Security_Acls_IObjectIdentity} (never <tt>null</tt>)
     * @throws Bee_Security_Acls_Exception_NotFound if an {@link Acl} was not found for the requested {@link ObjectIdentity}
     */
    public function readAclForOidAndSids(Bee_Security_Acls_IObjectIdentity $object, $sids);

    /**
     * Obtains all the <tt>Acl</tt>s that apply for the passed <tt>Object</tt>s.<p>The returned map is
     * keyed on the passed objects, with the values being the <tt>Acl</tt> instances. Any unknown objects will not
     * have a map key.</p>
     *
     * @param $objects the objects to find {@link Acl} information for
     * @return Bee_Security_Acls_IAcl[] a map with exactly one element for each {@link ObjectIdentity} passed as an argument (never <tt>null</tt>)
     * @throws Bee_Security_Acls_Exception_NotFound if an {@link Acl} was not found for each requested {@link ObjectIdentity}
     */
    public function readAclsForOids($objects);

    /**
     * Obtains all the <tt>Acl</tt>s that apply for the passed <tt>Object</tt>s, but only for the
     * security identifies passed.<p>Implementations <em>MAY</em> provide a subset of the ACLs via this method
     * although this is NOT a requirement. This is intended to allow performance optimisations within implementations.
     * Callers should therefore use this method in preference to the alternative overloaded version which does not
     * have performance optimisation opportunities.</p>
     *  <p>The returned map is keyed on the passed objects, with the values being the <tt>Acl</tt>
     * instances. Any unknown objects (or objects for which the interested <tt>Sid</tt>s do not have entries) will
     * not have a map key.</p>
     *
     * @abstract
     * @param Bee_Security_Acls_IObjectIdentity[] $objects the objects to find {@link Acl} information for
     * @param Bee_Security_Acls_ISid[] $sids the security identities for which  {@link Acl} information is required
     *        (may be <tt>null</tt> to denote all entries)
     * @return Bee_Security_Acls_IAcl[] a map with exactly one element for each {@link Bee_Security_Acls_IObjectIdentity} passed as an argument (never <tt>null</tt>)
     * @throws Bee_Security_Acls_Exception_NotFound if an {@link Acl} was not found for each requested {@link ObjectIdentity}
     */
    public function readAclsForOidsAndSids($objects, $sids, $check = true);
}
