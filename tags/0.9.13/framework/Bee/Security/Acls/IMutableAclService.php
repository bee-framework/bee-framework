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
 * Time: 7:02:15 PM
 */

interface Bee_Security_Acls_IMutableAclService extends Bee_Security_Acls_IAclService {

    /**
     * Creates an empty <code>Acl</code> object in the database. It will have no entries. The returned object
     * will then be used to add entries.
     *
     * @param Bee_Security_Acls_IObjectIdentity $objectIdentity the object identity to create
     * @return Bee_Security_Acls_IMutableAcl an ACL object with its ID set
     * @throws Bee_Security_Acls_Exception_AlreadyExists if the passed object identity already has a record
     */
    function createAcl(Bee_Security_Acls_IObjectIdentity $objectIdentity);

    /**
     * Removes the specified entry from the database.
     *
     * @param objectIdentity the object identity to remove
     * @param deleteChildren whether to cascade the delete to children
     * @return void
     * @throws Bee_Security_Acls_Exception_ChildrenExist if the deleteChildren argument was <code>false</code> but children exist
     */
    function deleteAcl(Bee_Security_Acls_IObjectIdentity $objectIdentity, $deleteChildren);

    /**
     * Changes an existing <code>Acl</code> in the database.
     *
     * @param Bee_Security_Acls_IMutableAcl $acl to modify
     * @return Bee_Security_Acls_IMutableAcl
     * @throws Bee_Security_Acls_Exception_NotFound if the relevant record could not be found (did you remember to use {@link
     *         #createAcl(ObjectIdentity)} to create the object, rather than creating it with the <code>new</code>
     *         keyword?)
     */
    function updateAcl(Bee_Security_Acls_IMutableAcl $acl);

    /**
     * @param Bee_Security_Acls_IObjectIdentity $origObjectOid
     * @param Bee_Security_Acls_IObjectIdentity $targetObjectOid
     * @return void
     */
    function copyAcls(Bee_Security_Acls_IObjectIdentity $origObjectOid, Bee_Security_Acls_IObjectIdentity $targetObjectOid);

}
?>
