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
 * Time: 7:10:12 PM
 */

class Bee_Security_Acls_Pdo_AclService implements Bee_Security_Acls_IMutableAclService {

    const SELECT_ACL_OBJECT_WITH_PARENT = 'select obj.object_id_identity as identifier, class.class as type
        from acl_object_identity obj, acl_object_identity parent, acl_class class #
        where obj.parent_object = parent.id and obj.object_id_class = class.id
        and parent.object_id_identity = ? and parent.object_id_class = (
        select id FROM acl_class where acl_class.class = ?)';

    const SELECT_OBJECT_IDENTITY_PRIMARY_KEY = 'select acl_object_identity.id from acl_object_identity, acl_class
        where acl_object_identity.object_id_class = acl_class.id and acl_class.class = ?
        and acl_object_identity.object_id_identity = ?';
    const INSERT_OBJECT_IDENTITY = 'insert into acl_object_identity
        (object_id_class, object_id_identity, owner_sid, entries_inheriting) values (?, ?, ?, ?)';
    const UPDATE_OBJECT_IDENTITY = 'update acl_object_identity set
        parent_object = ?, owner_sid = ?, entries_inheriting = ? where id = ?';
    const DELETE_OBJECT_IDENTITY_BY_PRIMARY_KEY = 'delete from acl_object_identity where id=?';

    const INSERT_ENTRY = 'insert into acl_entry
        (acl_object_identity, ace_order, sid, mask, granting, audit_success, audit_failure)
        values (?, ?, ?, ?, ?, ?, ?)';
    const DELETE_ENTRY_BY_OBJECT_IDENTITY_FK = 'delete from acl_entry where acl_object_identity=?';

    const INSERT_SID = 'insert into acl_sid (principal, sid) values (?, ?)';
    const SELECT_SID_PRIMARY_KEY = 'select id from acl_sid where principal = ? and sid = ?';

    const INSERT_CLASS = 'insert into acl_class (class) values (?)';
    const SELECT_CLASS_PRIMARY_KEY = "select id from acl_class where class=?";

    /**
     * @var Bee_Persistence_Pdo_Template
     */
    protected $pdoTemplate;

    /**
     * @var Bee_Security_Acls_Pdo_ILookupStrategy
     */
    private $lookupStrategy;

    /**
     * @var bool
     */
    private $foreignKeysInDatabase;

    public function __construct(PDO $pdoConnecton, Bee_Security_Acls_Pdo_ILookupStrategy $lookupStrategy) {
        $this->pdoTemplate = new Bee_Persistence_Pdo_Template($pdoConnecton);
        $this->lookupStrategy = $lookupStrategy;
    }

    public function findChildren(Bee_Security_Acls_IObjectIdentity $parentIdentity) {
        $args = array($parentIdentity->getIdentifier(), $parentIdentity->getType());
        $objects = $this->pdoTemplate->queryBySqlString(self::SELECT_ACL_OBJECT_WITH_PARENT,
            new Bee_Persistence_Pdo_StatementSetter_Args($args),
            new Bee_Persistence_Pdo_ResultSetExtractor_RowMapper(new Bee_Security_Acls_Pdo_AclService_RowMapper_findChildren()));

        if (count($objects) == 0) {
        	return null;
        }

        return $objects;
    }

    public function readAclForOidAndSids(Bee_Security_Acls_IObjectIdentity $object, $sids) {
        $map = $this->readAclsForOidsAndSids(array($object), $sids);
        Bee_Utils_Assert::isTrue(array_key_exists($object->getIdentifierString(), $map), 'There should have been an Acl entry for ObjectIdentity '.$object->getIdentifierString());
        return $map[$object->getIdentifierString()];

    }

    public function readAclForOid(Bee_Security_Acls_IObjectIdentity $object) {
        return $this->readAclForOidAndSids($object, null);
    }

    public function readAclsForOids($objects) {
        return $this->readAclsForOidsAndSids($objects, null);
    }

    /**
     * @throws NotFoundException
     * @param Bee_Security_Acls_IObjectIdentity[] $objects 
     * @param  $sids
     * @return #M#P#CBee_Security_Acls_Pdo_AclService.lookupStrategy.readAclsByOidsAndSids|?
     */
    public function readAclsForOidsAndSids($objects, $sids, $check = true) {
        $result = $this->lookupStrategy->readAclsByOidsAndSids($objects, $sids);

        if($check) {
            // Check every requested object identity was found (throw NotFoundException if needed)
            foreach($objects as $oid) {
                if(!array_key_exists($oid->getIdentifierString(), $result)) {
                    throw new Bee_Security_Acls_Exception_NotFound('Unable to find ACL information for object identity '. $oid->getIdentifierString());
                }
            }
        }

        return $result;
    }

    public function createAcl(Bee_Security_Acls_IObjectIdentity $objectIdentity) {
        Bee_Utils_Assert::notNull($objectIdentity, 'Object Identity required');

        // Check this object identity hasn't already been persisted
        if (!is_null($this->retrieveObjectIdentityPrimaryKey($objectIdentity))) {
            throw new Bee_Security_Acls_Exception_AlreadyExists('Object identity '.$objectIdentity->getIdentifierString().' already exists');
        }

        // Need to retrieve the current principal, in order to know who "owns" this ACL (can be changed later on)
        $auth = Bee_Security_Context_Holder::getContext()->getAuthentication();
        $sid = new Bee_Security_Acls_Impl_PrincipalSid($auth);

        // Create the acl_object_identity row
        $this->createObjectIdentity($objectIdentity, $sid);

        // Retrieve the ACL via superclass (ensures cache registration, proper retrieval etc)
        $acl = $this->readAclForOid($objectIdentity);
        Bee_Utils_Assert::isInstanceOf('Bee_Security_Acls_IMutableAcl', $acl, "MutableAcl should have been returned");

        return $acl;

    }

    public function deleteAcl(Bee_Security_Acls_IObjectIdentity $objectIdentity, $deleteChildren) {
        Bee_Utils_Assert::notNull($objectIdentity, 'Object Identity required');
        Bee_Utils_Assert::notNull($objectIdentity->getIdentifier(), 'Object Identity doesn\'t provide an identifier');

        if ($deleteChildren) {
        	$children = $this->findChildren($objectIdentity);
        	if ($children != null) {
                foreach($children as $child) {
                    $this->deleteAcl($child, true);
                }
        	}
        } else {
        	if (!$this->foreignKeysInDatabase) {
        		// We need to perform a manual verification for what a FK would normally do
        		// We generally don't do this, in the interests of deadlock management
        		$children = $this->findChildren($objectIdentity);
        		if ($children != null) {
                    throw new Bee_Security_Acls_Exception_ChildrenExist('Cannot delete '.$objectIdentity->getIdentifierString().' (has '.count($children).' children)');
        		}
        	}
        }

        $oidPrimaryKey = $this->retrieveObjectIdentityPrimaryKey($objectIdentity);

        // Delete this ACL's ACEs in the acl_entry table
        $this->deleteEntries($oidPrimaryKey);

        // Delete this ACL's acl_object_identity row
        $this->deleteObjectIdentity($oidPrimaryKey);

        // Clear the cache
//        aclCache.evictFromCache(objectIdentity);
    }

    public function updateAcl(Bee_Security_Acls_IMutableAcl $acl) {
        Bee_Utils_Assert::notNull($acl->getId(), 'Object Identity doesn\'t provide an identifier');

        // Delete this ACL's ACEs in the acl_entry table
        $this->deleteEntries($this->retrieveObjectIdentityPrimaryKey($acl->getObjectIdentity()));

        // Create this ACL's ACEs in the acl_entry table
        $this->createEntries($acl);

        // Change the mutable columns in acl_object_identity
        $this->updateObjectIdentity($acl);

        // Clear the cache, including children
//        clearCacheIncludingChildren(acl.getObjectIdentity());

        // Retrieve the ACL via superclass (ensures cache registration, proper retrieval etc)
        return $this->readAclForOid($acl->getObjectIdentity());

    }

    /**
     * Retrieves the primary key from the acl_object_identity table for the passed ObjectIdentity. Unlike some
     * other methods in this implementation, this method will NOT create a row (use {@link
     * #createObjectIdentity(ObjectIdentity, Sid)} instead).
     *
     * @param oid to find
     *
     * @return the object identity or null if not found
     */
    protected function retrieveObjectIdentityPrimaryKey(Bee_Security_Acls_IObjectIdentity $oid) {
        try {
            return $this->pdoTemplate->queryScalarBySqlStringAndArgsArray(self::SELECT_OBJECT_IDENTITY_PRIMARY_KEY,
                array($oid->getType(), $oid->getIdentifier()));
        } catch (Bee_Persistence_Exception_DataAccess $notFound) {
            return null;
        }
    }

    /**
     * Creates an entry in the acl_object_identity table for the passed ObjectIdentity. The Sid is also
     * necessary, as acl_object_identity has defined the sid column as non-null.
     *
     * @param object to represent an acl_object_identity for
     * @param owner for the SID column (will be created if there is no acl_sid entry for this particular Sid already)
     */
    protected function createObjectIdentity(Bee_Security_Acls_IObjectIdentity $object, Bee_Security_Acls_ISid $owner) {
        $sidId = $this->createOrRetrieveSidPrimaryKey($owner, true);
        $classId = $this->createOrRetrieveClassPrimaryKey($object->getType(), true);
        $this->pdoTemplate->updateBySqlStringAndArgsArray(self::INSERT_OBJECT_IDENTITY,
            array($classId, $object->getIdentifier(), $sidId, true));
    }

    /**
     * Retrieves the primary key from acl_class, creating a new row if needed and the allowCreate property is
     * true.
     *
     * @param clazz to find or create an entry for (this implementation uses the fully-qualified class name String)
     * @param allowCreate true if creation is permitted if not found
     *
     * @return the primary key or null if not found
     */
    protected function createOrRetrieveClassPrimaryKey($className, $allowCreate) {
        return $this->createOrRetrieve(self::SELECT_CLASS_PRIMARY_KEY, self::INSERT_CLASS,
            array($className), $allowCreate);
    }

    /**
     * Retrieves the primary key from acl_sid, creating a new row if needed and the allowCreate property is
     * true.
     *
     * @param sid to find or create
     * @param allowCreate true if creation is permitted if not found
     *
     * @return the primary key or null if not found
     *
     * @throws InvalidArgumentException DOCUMENT ME!
     */
    function createOrRetrieveSidPrimaryKey(Bee_Security_Acls_ISid $sid, $allowCreate) {
        Bee_Utils_Assert::notNull($sid, 'Sid required');

        $sidName = null;
        $principal = true;

        if ($sid instanceof Bee_Security_Acls_Impl_PrincipalSid) {
            $sidName = $sid->getPrincipal();
        } else if ($sid instanceof Bee_Security_Acls_Impl_GrantedAuthoritySid) {
            $sidName = $sid->getGrantedAuthority();
            $principal = false;
        } else {
            throw new InvalidArgumentException('Unsupported implementation of Sid');
        }

        return $this->createOrRetrieve(self::SELECT_SID_PRIMARY_KEY, self::INSERT_SID,
            array($principal, $sidName), $allowCreate);
    }

    private function createOrRetrieve($selectSql, $insertSql, array $params, $allowCreate) {
        $ids = $this->pdoTemplate->queryColumnBySqlStringAndArgsArray($selectSql, $params);
        $id = null;

        if (count($ids) == 0) {
            if ($allowCreate) {
                $this->pdoTemplate->updateBySqlStringAndArgsArray($insertSql, $params);
                $id = $this->pdoTemplate->queryScalarBySqlStringAndArgsArray($selectSql, $params);
            }
        } else {
            $id = $ids[0];
        }

        return $id;
    }

    /**
     * Updates an existing acl_object_identity row, with new information presented in the passed MutableAcl
     * object. Also will create an acl_sid entry if needed for the Sid that owns the MutableAcl.
     *
     * @param acl to modify (a row must already exist in acl_object_identity)
     *
     * @throws NotFoundException DOCUMENT ME!
     */
    protected function updateObjectIdentity(Bee_Security_Acls_IMutableAcl $acl) {
        $parentId = null;

        if ($acl->getParentAcl() != null) {
            // I don't see a reason for this ...?
//            Bee_Utils_Assert::isInstanceOf('Bee_Security_Acls_Impl_ObjectIdentity', $acl->getParentAcl()->getObjectIdentity(),
//                'Implementation only supports ObjectIdentityImpl');

            $oii = $acl->getParentAcl()->getObjectIdentity();
            $parentId = $this->retrieveObjectIdentityPrimaryKey($oii);
        }

        Bee_Utils_Assert::notNull($acl->getOwner(), 'Owner is required in this implementation');

        $ownerSid = $this->createOrRetrieveSidPrimaryKey($acl->getOwner(), true);
        $count = $this->pdoTemplate->updateBySqlStringAndArgsArray(self::UPDATE_OBJECT_IDENTITY,
                array($parentId, $ownerSid, $acl->isEntriesInheriting(), $acl->getId()));

        // this doesn't actually work on MySQL, as updating a row without performing any actual changes results in a row count of 0
//        if ($count != 1) {
//            throw new Bee_Security_Acls_Exception_NotFound('Unable to locate ACL to update');
//        }
    }

    /**
     * Creates a new row in acl_entry for every ACE defined in the passed MutableAcl object.
     *
     * @param acl containing the ACEs to insert
     */
    protected function createEntries(Bee_Security_Acls_IMutableAcl $acl) {
        $this->pdoTemplate->batchUpdateBySqlString(self::INSERT_ENTRY,
            new Bee_Security_Acls_Pdo_AclService_BatchStatementSetter_createEntries($acl, $this));
    }

    /**
     * Deletes all ACEs defined in the acl_entry table belonging to the presented ObjectIdentity primary key.
     *
     * @param oidPrimaryKey the rows in acl_entry to delete
     */
    protected function deleteEntries($oidPrimaryKey) {
    	$this->pdoTemplate->updateBySqlStringAndArgsArray(self::DELETE_ENTRY_BY_OBJECT_IDENTITY_FK, array($oidPrimaryKey));
    }

    /**
     * Deletes a single row from acl_object_identity that is associated with the presented ObjectIdentity primary key.
     *
     * <p>
     * We do not delete any entries from acl_class, even if no classes are using that class any longer. This is a
     * deadlock avoidance approach.
     * </p>
     *
     * @param oidPrimaryKey to delete the acl_object_identity
     */
    protected function deleteObjectIdentity($oidPrimaryKey) {
        // Delete the acl_object_identity row
        $this->pdoTemplate->updateBySqlStringAndArgsArray(self::DELETE_OBJECT_IDENTITY_BY_PRIMARY_KEY, array($oidPrimaryKey));
    }

    public function copyAcls(Bee_Security_Acls_IObjectIdentity $origObjectOid, Bee_Security_Acls_IObjectIdentity $targetObjectOid) {
        try {
            $origAcl = $this->readAclForOid($origObjectOid);
            try {
                $targetAcl = $this->readAclForOid($targetObjectOid);

            } catch (Exception $e) {
                $targetAcl = $this->createAcl($targetObjectOid);
            }

            $count = 0;
            foreach ($origAcl->getEntries() as $origAce) {
                $targetAcl->insertAce($count++, $origAce->getPermission(), $origAce->getSid(), $origAce->isGranting());
            }
            $this->updateAcl($targetAcl);

        } catch (Bee_Security_Acls_Exception_NotFound $e) {
            // this means no acls are set => nothing to copy
        }

    }
}

class Bee_Security_Acls_Pdo_AclService_RowMapper_findChildren implements Bee_Persistence_Pdo_IRowMapper {
    public function mapRow(PDOStatement $rs, $rowNum) {
        return $rs->fetchObject('Bee_Security_Acls_Impl_ObjectIdentity');
    }
}

class Bee_Security_Acls_Pdo_AclService_BatchStatementSetter_createEntries implements Bee_Persistence_Pdo_IBatchStatementSetter {

    /**
     * @var Bee_Security_Acls_IMutableAcl
     */
    private $acl;

    /**
     * @var Bee_Security_Acls_Pdo_AclService
     */
    private $aclService;

    public function __construct(Bee_Security_Acls_IMutableAcl $acl, Bee_Security_Acls_Pdo_AclService $aclService) {
        $this->acl = $acl;
        $this->aclService = $aclService;
    }

    public function setValues(PDOStatement $ps, $i) {
        $entries = $this->acl->getEntries();
        $entry = $entries[$i];

        $ps->bindValue(1, $this->acl->getId());
        $ps->bindValue(2, $i);
        $ps->bindValue(3, $this->aclService->createOrRetrieveSidPrimaryKey($entry->getSid(), true));
        $ps->bindValue(4, $entry->getPermission()->getMask());
        $ps->bindValue(5, $entry->isGranting());
        $ps->bindValue(6, $entry->isAuditSuccess());
        $ps->bindValue(7, $entry->isAuditFailure());
    }

    public function getBatchSize() {
        return count($this->acl->getEntries());
    }

}