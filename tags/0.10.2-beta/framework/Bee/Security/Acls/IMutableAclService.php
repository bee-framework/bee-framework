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
use Bee\Security\Acls\Exception\AlreadyExistsException;
use Bee\Security\Acls\Exception\ChildrenExistException;
use Bee\Security\Acls\Exception\NotFoundException;

/**
 * Interface IMutableAclService
 * @package Bee\Security\Acls
 */
interface IMutableAclService extends IAclService {

    /**
     * Creates an empty <code>Acl</code> object in the database. It will have no entries. The returned object
     * will then be used to add entries.
     *
     * @param IObjectIdentity $objectIdentity the object identity to create
     * @return IMutableAcl an ACL object with its ID set
     * @throws AlreadyExistsException if the passed object identity already has a record
     */
    function createAcl(IObjectIdentity $objectIdentity);

    /**
     * Removes the specified entry from the database.
     *
     * @param IObjectIdentity $objectIdentity the object identity to remove
     * @param boolean $deleteChildren whether to cascade the delete to children
     * @return void
     * @throws ChildrenExistException if the deleteChildren argument was <code>false</code> but children exist
     */
    function deleteAcl(IObjectIdentity $objectIdentity, $deleteChildren);

    /**
     * Changes an existing <code>Acl</code> in the database.
     *
     * @param IMutableAcl $acl to modify
     * @return IMutableAcl
     * @throws NotFoundException if the relevant record could not be found (did you remember to use {@link
     *         #createAcl(ObjectIdentity)} to create the object, rather than creating it with the <code>new</code>
     *         keyword?)
     */
    function updateAcl(IMutableAcl $acl);

    /**
     * @param IObjectIdentity $origObjectOid
     * @param IObjectIdentity $targetObjectOid
     * @return void
     */
    function copyAcls(IObjectIdentity $origObjectOid, IObjectIdentity $targetObjectOid);

}