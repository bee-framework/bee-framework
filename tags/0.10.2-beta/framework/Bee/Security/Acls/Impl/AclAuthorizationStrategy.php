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
use Bee\Security\Acls\IAcl;
use Bee\Security\Acls\IAclAuthorizationStrategy;
use Bee\Security\Acls\IPermission;
use Bee\Security\Acls\ISidRetrievalStrategy;
use Bee\Security\Context\SecurityContextHolder;
use Bee\Security\Exception\AccessDeniedException;

/**
 * User: mp
 * Date: Mar 23, 2010
 * Time: 8:59:57 AM
 */

class AclAuthorizationStrategy implements IAclAuthorizationStrategy {

    /**
     * @var ISidRetrievalStrategy
     */
    private $sidRetrievalStrategy;

    /**
     * @var IPermission[]
     */
    private $requirePermission;

    /**
     * @param IPermission[] $requirePermission
     */
    public function __construct(array $requirePermission) {
        $this->sidRetrievalStrategy = new SidRetrievalStrategy();
        $this->requirePermission = $requirePermission;
    }

    /**
     * Gets the SidRetrievalStrategy
     *
     * @return ISidRetrievalStrategy $sidRetrievalStrategy
     */
    public function getSidRetrievalStrategy() {
        return $this->sidRetrievalStrategy;
    }

    /**
     * Sets the SidRetrievalStrategy
     *
     * @param $sidRetrievalStrategy ISidRetrievalStrategy
     * @return void
     */
    public function setSidRetrievalStrategy(ISidRetrievalStrategy $sidRetrievalStrategy) {
        $this->sidRetrievalStrategy = $sidRetrievalStrategy;
    }

	/**
	 * @param IAcl $acl
	 * @param $changeType
	 * @throws AccessDeniedException
	 *
	 * todo: what is this????
	 */
    public function securityCheck(IAcl $acl, $changeType) {
        return;
        if ((is_null(SecurityContextHolder::getContext()))
            || (is_null(SecurityContextHolder::getContext()->getAuthentication()))
            || !SecurityContextHolder::getContext()->getAuthentication()->isAuthenticated()) {
            throw new AccessDeniedException('Authenticated principal required to operate with ACLs');
        }

        $authentication = SecurityContextHolder::getContext()->getAuthentication();

        // Check if authorized by virtue of ACL ownership
        $currentUser = new PrincipalSid($authentication);

        if ($currentUser->equals($acl->getOwner())
                && (($changeType == self::CHANGE_GENERAL) || ($changeType == self::CHANGE_OWNERSHIP))) {
            return;
        }

        // Not authorized by ACL ownership; try via adminstrative permissions
//        $requiredAuthority = null;
//
//        if ($changeType == self::CHANGE_AUDITING) {
//            $requiredAuthority = $this->gaModifyAuditing;
//        } else if ($changeType == self::CHANGE_GENERAL) {
//            $requiredAuthority = $this->gaGeneralChanges;
//        } else if ($changeType == self::CHANGE_OWNERSHIP) {
//            $requiredAuthority = $this->gaTakeOwnership;
//        } else {
//            throw new InvalidArgumentException('Unknown change type');
//        }
//
//        if($requiredAuthority instanceof IGrantedAuthority) {
//            $requiredAuthority = $requiredAuthority->getAuthority();
//        }

        // Iterate this principal's authorities to determine right
//        $auths = $authentication->getAuthorities();
//
//        foreach($auths as $authString => $auth) {
//            if ($requiredAuthority == $authString) {
//                return;
//            }
//        }

        // Try to get permission via ACEs within the ACL
        $sids = $this->sidRetrievalStrategy->getSids($authentication);

        if ($acl->isGranted($this->requirePermission, $sids, false)) {
            return;
        }

        throw new AccessDeniedException(
            'Principal does not have required ACL permissions to perform requested operation');
    }
}
