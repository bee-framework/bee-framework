<?php
namespace Bee\Security\Acls\Impl;
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
use Bee\Security\Acls\IAccessControlEntry;
use Bee\Security\Acls\IAcl;
use Bee\Security\Acls\IAclAuthorizationStrategy;
use Bee\Security\Acls\IAuditLogger;
use Bee\Security\Acls\IMutableAcl;
use Bee\Security\Acls\IObjectIdentity;
use Bee\Security\Acls\IPermission;
use Bee\Security\Acls\ISid;
use Bee\Utils\Assert;

/**
 * Class Acl
 * @package Bee\Security\Acls\Impl
 */
class Acl implements IAcl, IMutableAcl {

    /**
     * @var mixed
     */
    private $id;

    /**
     * 
     * @var AccessControlEntry[]
     */
    private $aces = array();

    /**
     * Associative array of $ace->getId() -> $ace mappings
     *
     * @var AccessControlEntry[]
     */
    private $acesById = array();

    /**
     * @var IObjectIdentity
     */
    private $objectIdentity;

    /**
     * @var ISid
     */
    private $owner;

    /**
     * @var IAcl
     */
    private $parentAcl;

    /**
     * @var boolean
     */
    private $entriesInheriting;

    /**
     * @var ISid[]
     */
    private $loadedSids = null;

    /**
     * @var IAuditLogger
     */
    private $auditLogger;

    /**
     * @var IAclAuthorizationStrategy
     */
    private $aclAuthorizationStrategy;

	/**
	 * @param IObjectIdentity $objectIdentity
	 * @param $id
	 * @param IAclAuthorizationStrategy $aclAuthorizationStrategy
	 * @param IAuditLogger $auditLogger
	 * @param IAcl $parentAcl
	 * @param array $loadedSids
	 * @param $entriesInheriting
	 * @param ISid $owner
	 */
    public function __construct(IObjectIdentity $objectIdentity, $id, IAclAuthorizationStrategy $aclAuthorizationStrategy,
        IAuditLogger $auditLogger, IAcl $parentAcl = null, array $loadedSids = null, $entriesInheriting, ISid $owner) {
        Assert::notNull($objectIdentity, 'Object Identity required');
        Assert::notNull($id, 'Id required');
        Assert::notNull($aclAuthorizationStrategy, 'AclAuthorizationStrategy required');
        Assert::notNull($owner, 'Owner required');
        Assert::notNull($auditLogger, 'AuditLogger required');
        $this->objectIdentity = $objectIdentity;
        $this->id = $id;
        $this->aclAuthorizationStrategy = $aclAuthorizationStrategy;
        $this->auditLogger = $auditLogger;
        $this->parentAcl = $parentAcl; // may be null
        $this->loadedSids = $loadedSids; // may be null
        $this->entriesInheriting = (bool) $entriesInheriting;
        $this->owner = $owner;
    }

    /**
     * @return AccessControlEntry[]
     */
    public function getEntries() {
        return $this->aces;
    }

	/**
	 * @return IObjectIdentity
	 */
    public function getObjectIdentity() {
        return $this->objectIdentity;
    }

	/**
	 * @return ISid
	 */
    public function getOwner() {
        return $this->owner;
    }

	/**
	 * @return IAcl
	 */
    public function getParentAcl() {
        return $this->parentAcl;
    }

	/**
	 * @return bool
	 */
    public function isEntriesInheriting() {
        return $this->entriesInheriting;
    }

    /**
     * @param IPermission[] $permissions
     * @param ISid[] $sids
     * @param boolean $administrativeMode
     * @return bool|string
     * @throws UnloadedSidException|NotFoundException
     */
    public function isGranted(array $permissions, array $sids, $administrativeMode) {
        Assert::notEmpty($permissions, 'Permissions required');
        Assert::notEmpty($sids, 'SIDs required');

        if (!$this->isSidLoaded($sids)) {
            throw new UnloadedSidException('ACL was not loaded for one or more SID');
        }

        $firstRejection = null;

        foreach($permissions as $permission) {
            foreach($sids as $sid) {

                // Attempt to find exact match for this permission mask and SID
                $scanNextSid = true;

                foreach($this->aces as $ace) {

                    if (($ace->getPermission()->equals($permission)) && $ace->getSid()->equals($sid)) {
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
            throw new NotFoundException('Unable to locate a matching ACE for passed permissions and SIDs');
        }

    }

    /**
     * @param ISid[] $sids
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

	/**
	 * @param int $aceIndex
	 * @throws NotFoundException
	 */
    public function deleteAce($aceIndex) {
        $this->aclAuthorizationStrategy->securityCheck($this, IAclAuthorizationStrategy::CHANGE_GENERAL);
        $this->verifyAceIndexExists($aceIndex);

        $ace = $this->aces[$aceIndex];
        unset($this->aces[$aceIndex]);
        unset($this->acesById['_'.$ace->getId()]);
        $this->aces = array_merge($this->aces);
    }

	/**
	 * @return int|mixed
	 */
    public function getId() {
        return $this->id;
    }

	/**
	 * @param int $atIndexLocation
	 * @param IPermission $permission
	 * @param ISid $sid
	 * @param bool $granting
	 * @throws NotFoundException
	 */
    public function insertAce($atIndexLocation, IPermission $permission, ISid $sid, $granting) {
        $this->aclAuthorizationStrategy->securityCheck($this, IAclAuthorizationStrategy::CHANGE_GENERAL);
        Assert::notNull($permission, 'Permission required');
        Assert::notNull($sid, 'Sid required');
        if ($atIndexLocation < 0) {
        	throw new NotFoundException('atIndexLocation must be greater than or equal to zero');
        }
        if ($atIndexLocation > count($this->aces)) {
        	throw new NotFoundException('atIndexLocation must be less than or equal to the size of the AccessControlEntry collection');
        }

        $ace = new AccessControlEntry(null, $this, $sid, $permission, $granting, false, false);

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

	/**
	 * @param ISid $newOwner
	 */
    public function setOwner(ISid $newOwner) {
        $this->aclAuthorizationStrategy->securityCheck($this, IAclAuthorizationStrategy::CHANGE_OWNERSHIP);
		Assert::notNull($newOwner, 'Owner required');
        $this->owner = $newOwner;
    }

	/**
	 * @param bool $entriesInheriting
	 */
    public function setEntriesInheriting($entriesInheriting) {
        $this->aclAuthorizationStrategy->securityCheck($this, IAclAuthorizationStrategy::CHANGE_GENERAL);
        $this->entriesInheriting = $entriesInheriting;
    }

	/**
	 * @param IAcl $newParent
	 */
    public function setParent(IAcl $newParent) {
        $this->aclAuthorizationStrategy->securityCheck($this, IAclAuthorizationStrategy::CHANGE_GENERAL);
		Assert::isTrue(is_null($newParent) || $newParent != $this, 'Cannot be the parent of yourself');
        $this->parentAcl = $newParent;
    }

	/**
	 * @param int $aceIndex
	 * @param IPermission $permission
	 * @throws NotFoundException
	 */
    public function updateAce($aceIndex, IPermission $permission) {
        $this->aclAuthorizationStrategy->securityCheck($this, IAclAuthorizationStrategy::CHANGE_GENERAL);
        $this->verifyAceIndexExists($aceIndex);
        $this->aces[$aceIndex]->setPermission($permission);
    }

	/**
	 * @param $aceIndex
	 * @throws NotFoundException
	 */
    private function verifyAceIndexExists($aceIndex) {
        if ($aceIndex < 0) {
        	throw new NotFoundException('atIndexLocation must be greater than or equal to zero');
        }
        if ($aceIndex >= count($this->aces)) {
        	throw new NotFoundException('atIndexLocation must be less than or equal to the size of the AccessControlEntry collection');
        }
    }

	/**
	 * @param $aceId
	 * @return bool
	 */
    function hasAce($aceId) {
        return array_key_exists('_'.$aceId, $this->acesById);
    }

	/**
	 * @param IAccessControlEntry $ace
	 */
    function addAce(IAccessControlEntry $ace) {
        $this->aces[] = $ace;
        $this->acesById['_'.$ace->getId()] = $ace;
    }

	/**
	 * @param Acl $parentAcl
	 */
    function setParentAcl(Acl $parentAcl) {
        $this->parentAcl = $parentAcl;
    }
}