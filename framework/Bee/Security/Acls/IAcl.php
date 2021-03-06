<?php
namespace Bee\Security\Acls;
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
use Bee\Security\Acls\Exception\NotFoundException;
use Bee\Security\Acls\Exception\UnloadedSidException;

/**
 * Represents an access control list (ACL) for a domain object.
 *
 * <p>
 * An <tt>Acl</tt> represents all ACL entries for a given domain object. In
 * order to avoid needing references to the domain object itself, this
 * interface handles indirection between a domain object and an ACL object
 * identity via the {@link IObjectIdentity} interface.
 * </p>
 *
 * <p>
 * Implementing classes may elect to return instances that represent
 * {@link IPermission} information for either
 * some OR all {@link Bee_Security_Acls_ISid} instances. Therefore, an
 * instance may NOT necessarily contain ALL <tt>Sid</tt>s
 * for a given domain object.
 * </p>
 *
 * @author Ben Alex
 * @version $Id: Acl.java 2872 2008-04-05 20:43:10Z benalex $
 */
interface IAcl {
    
    /**
     * Returns all of the entries represented by the present <tt>Acl</tt>. Entries associated with
     * the <tt>Acl</tt> parents are not returned.
     *
     * <p>This method is typically used for administrative purposes.</p>
     *
     * <p>The order that entries appear in the array is important for methods declared in the
     * {@link MutableAcl} interface. Furthermore, some implementations MAY use ordering as
     * part of advanced permission checking.</p>
     *
     * <p>Do <em>NOT</em> use this method for making authorization decisions. Instead use {@link
     * #isGranted(Permission[], Sid[], boolean)}.</p>
     *
     * <p>This method must operate correctly even if the <tt>Acl</tt> only represents a subset of
     * <tt>Sid</tt>s. The caller is responsible for correctly handling the result if only a subset of
     * <tt>Sid</tt>s is represented.</p>
     *
     * @return IAccessControlEntry[] the list of entries represented by the <tt>Acl</tt>, or <tt>null</tt> if there are
     * no entries presently associated with this <tt>Acl</tt>.
     */
    public function getEntries();

    /**
     * Obtains the domain object this <tt>Acl</tt> provides entries for. This is immutable once an
     * <tt>Acl</tt> is created.
     *
     * @return IObjectIdentity the object identity (never <tt>null</tt>)
     */
    public function getObjectIdentity();

    /**
     * Determines the owner of the <tt>Acl</tt>. The meaning of ownership varies by implementation and is
     * unspecified.
     *
     * @return ISid the owner (may be <tt>null</tt> if the implementation does not use ownership concepts)
     */
    public function getOwner();

    /**
     * A domain object may have a parent for the purpose of ACL inheritance. If there is a parent, its ACL can
     * be accessed via this method. In turn, the parent's parent (grandparent) can be accessed and so on.
     *
     * <p>This method solely represents the presence of a navigation hierarchy between the parent <tt>Acl</tt> and this
     * <tt>Acl</tt>. For actual inheritance to take place, the {@link #isEntriesInheriting()} must also be
     * <tt>true</tt>.</p>
     *
     * <p>This method must operate correctly even if the <tt>Acl</tt> only represents a subset of
     * <tt>Sid</tt>s. The caller is responsible for correctly handling the result if only a subset of
     * <tt>Sid</tt>s is represented.</p>
     *
     * @return IAcl the parent <tt>Acl</tt> (may be <tt>null</tt> if this <tt>Acl</tt> does not have a parent)
     */
    public function getParentAcl();

    /**
     * Indicates whether the ACL entries from the {@link #getParentAcl()} should flow down into the current
     * <tt>Acl</tt>.<p>The mere link between an <tt>Acl</tt> and a parent <tt>Acl</tt> on its own
     * is insufficient to cause ACL entries to inherit down. This is because a domain object may wish to have entirely
     * independent entries, but maintain the link with the parent for navigation purposes. Thus, this method denotes
     * whether or not the navigation relationship also extends to the actual inheritance of entries.</p>
     *
     * @return boolean <tt>true</tt> if parent ACL entries inherit into the current <tt>Acl</tt>
     */
    public function isEntriesInheriting();

    /**
     * This is the actual authorization logic method, and must be used whenever ACL authorization decisions are
     * required.
     *
     * <p>An array of <tt>Sid</tt>s are presented, representing security identifies of the current
     * principal. In addition, an array of <tt>Permission</tt>s is presented which will have one or more bits set
     * in order to indicate the permissions needed for an affirmative authorization decision. An array is presented
     * because holding <em>any</em> of the <tt>Permission</tt>s inside the array will be sufficient for an
     * affirmative authorization.</p>
     *
     * <p>The actual approach used to make authorization decisions is left to the implementation and is not
     * specified by this interface. For example, an implementation <em>MAY</em> search the current ACL in the order
     * the ACL entries have been stored. If a single entry is found that has the same active bits as are shown in a
     * passed <tt>Permission</tt>, that entry's grant or deny state may determine the authorization decision. If
     * the case of a deny state, the deny decision will only be relevant if all other <tt>Permission</tt>s passed
     * in the array have also been unsuccessfully searched. If no entry is found that match the bits in the current
     * ACL, provided that {@link #isEntriesInheriting()} is <tt>true</tt>, the authorization decision may be
     * passed to the parent ACL. If there is no matching entry, the implementation MAY throw an exception, or make a
     * predefined authorization decision.</p>
     *
     * <p>This method must operate correctly even if the <tt>Acl</tt> only represents a subset of <tt>Sid</tt>s,
     * although the implementation is permitted to throw one of the signature-defined exceptions if the method
     * is called requesting an authorization decision for a {@link Sid} that was never loaded in this <tt>Acl</tt>.
     * </p>
     *
     * @param IPermission[] $permission the permission or permissions required (at least one entry required)
     * @param ISid[] $sids the security identities held by the principal (at least one entry required)
     * @param boolean $administrativeMode if <tt>true</tt> denotes the query is for administrative purposes and no logging
     *        or auditing (if supported by the implementation) should be undertaken
     *
     * @return boolean <tt>true</tt> if authorization is granted
     *
     * @throws NotFoundException MUST be thrown if an implementation cannot make an authoritative authorization
     *         decision, usually because there is no ACL information for this particular permission and/or SID
     * @throws UnloadedSidException thrown if the <tt>Acl</tt> does not have details for one or more of the
     *         <tt>Sid</tt>s passed as arguments
     */
    public function isGranted(array $permission, array $sids, $administrativeMode);

    /**
     * For efficiency reasons an <tt>Acl</tt> may be loaded and <em>not</em> contain entries for every
     * <tt>Sid</tt> in the system. If an <tt>Acl</tt> has been loaded and does not represent every
     * <tt>Sid</tt>, all methods of the <tt>Acl</tt> can only be used within the limited scope of the
     * <tt>Sid</tt> instances it actually represents.
     * <p>
     * It is normal to load an <tt>Acl</tt> for only particular <tt>Sid</tt>s if read-only authorization
     * decisions are being made. However, if user interface reporting or modification of <tt>Acl</tt>s are
     * desired, an <tt>Acl</tt> should be loaded with all <tt>Sid</tt>s. This method denotes whether or
     * not the specified <tt>Sid</tt>s have been loaded or not.
     * </p>
     *
     * @param ISid[] sids one or more security identities the caller is interest in
     *        knowing whether this <tt>Sid</tt> supports
     *
     * @return boolean <tt>true</tt> if every passed <tt>Sid</tt> is represented by this <tt>Acl</tt> instance
     */
    public function isSidLoaded(array $sids);
}