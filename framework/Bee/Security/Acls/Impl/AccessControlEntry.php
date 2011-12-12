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
 * Time: 1:11:14 AM
 */

class Bee_Security_Acls_Impl_AccessControlEntry implements Bee_Security_Acls_IAccessControlEntry, Bee_Security_Acls_IAuditableAccessControlEntry {

    /**
     * @var Bee_Security_Acls_IAcl
     */
    private $acl;

    /**
     * @var Bee_Security_Acls_IPermission
     */
    private $permission;

    /**
     * @var mixed
     */
    private $id;

    /**
     * @var Bee_Security_Acls_ISid
     */
    private $sid;

    /**
     * @var bool
     */
    private $granting;

    /**
     * @var bool
     */
    private $auditFailure;

    /**
     * @var bool
     */
    private $auditSuccess;

    public function __construct($id, Bee_Security_Acls_IAcl $acl, Bee_Security_Acls_ISid $sid,
                                Bee_Security_Acls_IPermission $permission, $granting, $auditSuccess, $auditFailure) {
        Bee_Utils_Assert::notNull($acl, 'Acl required');
        Bee_Utils_Assert::notNull($sid, 'Sid required');
        Bee_Utils_Assert::notNull($permission, 'Permission required');
        $this->id = $id;
        $this->acl = $acl; // can be null
        $this->sid = $sid;
        $this->permission = $permission;
        $this->granting = $granting;
        $this->auditSuccess = $auditSuccess;
        $this->auditFailure = $auditFailure;
    }

    public function getAcl() {
        return $this->acl;
    }

    public function setAcl(Bee_Security_Acls_IAcl $acl) {
        $this->acl = $acl;
    }

    public function getId() {
        return $this->id;
    }

    public function getPermission() {
        return $this->permission;
    }

    public function setPermission($permission) {
        $this->permission = $permission;
    }

    public function getSid() {
        return $this->sid;
    }

    public function isGranting() {
        return $this->granting;
    }

    public function isAuditFailure() {
        return $this->auditFailure;
    }

    public function isAuditSuccess() {
        return $this->auditSuccess;
    }

}
?>
