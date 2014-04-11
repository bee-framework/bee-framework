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
 * Date: Mar 23, 2010
 * Time: 8:59:57 AM
 */

class Bee_Security_Acls_Impl_AclAuthorizationStrategy implements Bee_Security_Acls_IAclAuthorizationStrategy {

    /**
     * @var Bee_Security_Acls_ISidRetrievalStrategy
     */
    private $sidRetrievalStrategy;

    /**
     * @var Bee_Security_Acls_IPermission[]
     */
    private $requirePermission;

    /**
     * @param Bee_Security_Acls_IPermission[] $requirePermission
     * @return void
     */
    public function __construct(array $requirePermission) {
        $this->sidRetrievalStrategy = new Bee_Security_Acls_Impl_SidRetrievalStrategy();
        $this->requirePermission = $requirePermission;
    }

    /**
     * Gets the SidRetrievalStrategy
     *
     * @return Bee_Security_Acls_ISidRetrievalStrategy $sidRetrievalStrategy
     */
    public function getSidRetrievalStrategy() {
        return $this->sidRetrievalStrategy;
    }

    /**
     * Sets the SidRetrievalStrategy
     *
     * @param $sidRetrievalStrategy Bee_Security_Acls_ISidRetrievalStrategy
     * @return void
     */
    public function setSidRetrievalStrategy(Bee_Security_Acls_ISidRetrievalStrategy $sidRetrievalStrategy) {
        $this->sidRetrievalStrategy = $sidRetrievalStrategy;
    }

    public function securityCheck(Bee_Security_Acls_IAcl $acl, $changeType) {
        return;
        if ((is_null(Bee_Security_Context_Holder::getContext()))
            || (is_null(Bee_Security_Context_Holder::getContext()->getAuthentication()))
            || !Bee_Security_Context_Holder::getContext()->getAuthentication()->isAuthenticated()) {
            throw new Bee_Security_Exception_AccessDenied('Authenticated principal required to operate with ACLs');
        }

        $authentication = Bee_Security_Context_Holder::getContext()->getAuthentication();

        // Check if authorized by virtue of ACL ownership
        $currentUser = new Bee_Security_Acls_Impl_PrincipalSid($authentication);

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
//        if($requiredAuthority instanceof Bee_Security_IGrantedAuthority) {
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

        throw new Bee_Security_Exception_AccessDenied(
            'Principal does not have required ACL permissions to perform requested operation');
    }
}
?>
