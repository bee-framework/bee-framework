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
 * Time: 12:15:11 AM
 */

class Bee_Security_Acls_Impl_Acl implements Bee_Security_Acls_IAcl, Bee_Security_Acls_IMutableAcl {

    /**
     * @var mixed
     */
    private $id;

    /**
     * 
     * @var Bee_Security_Acls_Impl_AccessControlEntry[]
     */
    private $aces = array();

    /**
     * Associative array of $ace->getId() -> $ace mappings
     *
     * @var Bee_Security_Acls_Impl_AccessControlEntry[]
     */
    private $acesById = array();

    /**
     * @var Bee_Security_Acls_IObjectIdentity
     */
    private $objectIdentity;

    /**
     * @var Bee_Security_Acls_ISid
     */
    private $owner;

    /**
     * @var Bee_Security_Acls_IAcl
     */
    private $parentAcl;

    /**
     * @var boolean
     */
    private $entriesInheriting;

    /**
     * @var Bee_Security_Acls_ISid[]
     */
    private $loadedSids = null;

    /**
     * @var Bee_Security_Acls_IAuditLogger
     */
    private $auditLogger;

    /**
     * @var Bee_Security_Acls_IAclAuthorizationStrategy
     */
    private $aclAuthorizationStrategy;

    public function __construct(Bee_Security_Acls_IObjectIdentity $objectIdentity, $id, Bee_Security_Acls_IAclAuthorizationStrategy $aclAuthorizationStrategy,
        Bee_Security_Acls_IAuditLogger $auditLogger, Bee_Security_Acls_IAcl $parentAcl = null, array $loadedSids = null, $entriesInheriting, Bee_Security_Acls_ISid $owner) {
        Bee_Utils_Assert::notNull($objectIdentity, 'Object Identity required');
        Bee_Utils_Assert::notNull($id, 'Id required');
        Bee_Utils_Assert::notNull($aclAuthorizationStrategy, 'AclAuthorizationStrategy required');
        Bee_Utils_Assert::notNull($owner, 'Owner required');
        Bee_Utils_Assert::notNull($auditLogger, 'AuditLogger required');
        $this->objectIdentity = $objectIdentity;
        $this->id = $id;
        $this->aclAuthorizationStrategy = $aclAuthorizationStrategy;
        $this->auditLogger = $auditLogger;
        $this->parentAcl = $parentAcl; // may be null
        $this->loadedSids = $loadedSids; // may be null
        $this->entriesInheriting = $entriesInheriting;
        $this->owner = $owner;
    }

    /**
     * @return Bee_Security_Acls_Impl_AccessControlEntry[]
     */
    public function getEntries() {
        return $this->aces;
    }

    public function getObjectIdentity() {
        return $this->objectIdentity;
    }

    public function getOwner() {
        return $this->owner;
    }

    public function getParentAcl() {
        return $this->parentAcl;
    }

    public function isEntriesInheriting() {
        return $this->entriesInheriting;
    }

    /**
     * @param Bee_Security_Acls_IPermission[] $permissions
     * @param Bee_Security_Acls_ISid[] $sids
     * @param boolean $administrativeMode
     * @return bool|string
     * @throws Bee_Security_Acls_Exception_UnloadedSid|Bee_Security_Acls_Exception_NotFound
     */
    public function isGranted(array $permissions, array $sids, $administrativeMode) {
        Bee_Utils_Assert::notEmpty($permissions, 'Permissions required');
        Bee_Utils_Assert::notEmpty($sids, 'SIDs required');

        if (!$this->isSidLoaded($sids)) {
            throw new Bee_Security_Acls_Exception_UnloadedSid('ACL was not loaded for one or more SID');
        }

        $firstRejection = null;

        foreach($permissions as $permission) {
            foreach($sids as $sid) {

                // Attempt to find exact match for this permission mask and SID
                $scanNextSid = true;

                foreach($this->aces as $ace) {

                    if (($ace->getPermission()->getMask() == $permission->getMask()) && $ace->getSid()->equals($sid)) {
                        // Found a matching ACE, so its authorization decision will prevail
                        if ($ace->isGranting()) {
                            // Success
                            if (!$administrativeMode) {
                                $this->auditLogger->logIfNeeded(true, $ace);
                            }

                            return true;
                        } else {
                            // Failure for this permission, so stop search
                            // We will see if they have a different permission
                            // (this permission is 100% rejected for this SID)
                            if ($firstRejection == null) {
                                // Store first rejection for auditing reasons
                                $firstRejection = $ace;
                            }

                            $scanNextSid = false; // helps break the loop

                            break; // exit "aceIterator" while loop
                        }
                    }
                }

                if (!$scanNextSid) {
                    break; // exit SID for loop (now try next permission)
                }
            }
        }

        if ($firstRejection != null) {
            // We found an ACE to reject the request at this point, as no
            // other ACEs were found that granted a different permission
            if (!$administrativeMode) {
                $this->auditLogger->logIfNeeded(false, $firstRejection);
            }

            return false;
        }

        // No matches have been found so far
        if ($this->isEntriesInheriting() && ($this->parentAcl != null)) {
            // We have a parent, so let them try to find a matching ACE
            return $this->parentAcl->isGranted($permissions, $sids, false);
        } else {
            // We either have no parent, or we're the uppermost parent
            throw new Bee_Security_Acls_Exception_NotFound('Unable to locate a matching ACE for passed permissions and SIDs');
        }

    }

    /**
     * @param Bee_Security_Acls_ISid[] $sids
     * @return bool
     */
    public function isSidLoaded(array $sids) {
        // If loadedSides is null, this indicates all SIDs were loaded
        // Also return true if the caller didn't specify a SID to find
        if (($this->loadedSids == null) || ($sids == null) || (count($sids) == 0)) {
            return true;
        }

        // todo: maybe use associative arrays based on the identifierString ans key matching for more oomph!?
        // This ACL applies to a SID subset only. Iterate to check it applies.
        foreach($sids as $sid) {
            $found = false;

            foreach($this->loadedSids as $loadedSid) {
                if ($sid->equals($loadedSid)) {
                    // this SID is OK
                    $found = true;
                    break; // out of loadedSids for loop
                }
            }

            if (!$found) {
                return false;
            }
        }

        return true;
    }

    public function deleteAce($aceIndex) {
        $this->aclAuthorizationStrategy->securityCheck($this, Bee_Security_Acls_IAclAuthorizationStrategy::CHANGE_GENERAL);
        $this->verifyAceIndexExists($aceIndex);

        $ace = $this->aces[$aceIndex];
        unset($this->aces[$aceIndex]);
        unset($this->acesById['_'.$ace->getId()]);
        $this->aces = array_merge($this->aces);
    }

    public function getId() {
        return $this->id;
    }

    public function insertAce($atIndexLocation, Bee_Security_Acls_IPermission $permission, Bee_Security_Acls_ISid $sid, $granting) {
        $this->aclAuthorizationStrategy->securityCheck($this, Bee_Security_Acls_IAclAuthorizationStrategy::CHANGE_GENERAL);
        Bee_Utils_Assert::notNull($permission, 'Permission required');
        Bee_Utils_Assert::notNull($sid, 'Sid required');
        if ($atIndexLocation < 0) {
        	throw new Bee_Security_Acls_Exception_NotFound('atIndexLocation must be greater than or equal to zero');
        }
        if ($atIndexLocation > count($this->aces)) {
        	throw new Bee_Security_Acls_Exception_NotFound('atIndexLocation must be less than or equal to the size of the AccessControlEntry collection');
        }

        $ace = new Bee_Security_Acls_Impl_AccessControlEntry(null, $this, $sid, $permission, $granting, false, false);

        if($atIndexLocation == 0) {
            array_unshift($this->aces, $ace);
        } else if($atIndexLocation == count($this->aces)) {
            array_push($this->aces, $ace);
        } else {
            $tmp = array_slice($this->aces, 0, $atIndexLocation);
            array_push($tmp, $ace);
            $this->aces = array_merge($tmp, array_slice($this->aces, $atIndexLocation));
        }
    }

    public function setOwner(Bee_Security_Acls_ISid $newOwner) {
        $this->aclAuthorizationStrategy->securityCheck($this, Bee_Security_Acls_IAclAuthorizationStrategy::CHANGE_OWNERSHIP);
        Bee_Utils_Assert::notNull($newOwner, 'Owner required');
        $this->owner = $newOwner;
    }

    public function setEntriesInheriting($entriesInheriting) {
        $this->aclAuthorizationStrategy->securityCheck($this, Bee_Security_Acls_IAclAuthorizationStrategy::CHANGE_GENERAL);
        $this->entriesInheriting = $entriesInheriting;
    }

    public function setParent(Bee_Security_Acls_IAcl $newParent) {
        $this->aclAuthorizationStrategy->securityCheck($this, Bee_Security_Acls_IAclAuthorizationStrategy::CHANGE_GENERAL);
        Bee_Utils_Assert::isTrue(is_null($newParent) || $newParent != $this, 'Cannot be the parent of yourself');
        $this->parentAcl = $newParent;
    }

    public function updateAce($aceIndex, Bee_Security_Acls_IPermission $permission) {
        $this->aclAuthorizationStrategy->securityCheck($this, Bee_Security_Acls_IAclAuthorizationStrategy::CHANGE_GENERAL);
        $this->verifyAceIndexExists($aceIndex);
        $this->aces[$aceIndex]->setPermission($permission);
    }

    private function verifyAceIndexExists($aceIndex) {
        if ($aceIndex < 0) {
        	throw new Bee_Security_Acls_Exception_NotFound('atIndexLocation must be greater than or equal to zero');
        }
        if ($aceIndex >= count($this->aces)) {
        	throw new Bee_Security_Acls_Exception_NotFound('atIndexLocation must be less than or equal to the size of the AccessControlEntry collection');
        }
    }

    function hasAce($aceId) {
        return array_key_exists('_'.$aceId, $this->acesById);
    }
    function addAce(Bee_Security_Acls_IAccessControlEntry $ace) {
        $this->aces[] = $ace;
        $this->acesById['_'.$ace->getId()] = $ace;
    }

    function setParentAcl(Bee_Security_Acls_Impl_Acl $parentAcl) {
        $this->parentAcl = $parentAcl;
    }
}
