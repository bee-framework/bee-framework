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

/**
 * User: mp
 * Date: Mar 16, 2010
 * Time: 6:31:44 PM
 */

interface IMutableAcl extends IAcl {

    /**
     * @abstract
     * @param int $aceIndex
     * @return void
     */
    public function deleteAce($aceIndex);

    /**
     * Obtains an identifier that represents this <tt>MutableAcl</tt>.
     *
     * @return int the identifier, or <tt>null</tt> if unsaved
     */
    public function getId();

    /**
     * @abstract
     * @param int $atIndexLocation
     * @param IPermission $permission
     * @param ISid $sid
     * @param boolean $granting
     * @return void
     */
    public function insertAce($atIndexLocation, IPermission $permission, ISid $sid, $granting);

    /**
     * Changes the present owner to a different owner.
     *
     * @param ISid $newOwner the new owner (mandatory; cannot be null)
     */
    public function setOwner(ISid $newOwner);

    /**
     * Change the value returned by {@link Acl#isEntriesInheriting()}.
     *
     * @param boolean $entriesInheriting the new value
     */
    public function setEntriesInheriting($entriesInheriting);

    /**
     * Changes the parent of this ACL.
     *
     * @param IAcl $newParent the new parent
     */
    public function setParent(IAcl $newParent);

    /**
     * @abstract
     * @param int $aceIndex
     * @param IPermission $permission
     * @return void
     */
    public function updateAce($aceIndex, IPermission $permission);
}
