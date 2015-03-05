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
use Bee\Security\Acls\IAccessControlEntry;
use Bee\Security\Acls\IAcl;
use Bee\Security\Acls\IAuditableAccessControlEntry;
use Bee\Security\Acls\IPermission;
use Bee\Security\Acls\ISid;
use Bee\Utils\Assert;

/**
 * Class AccessControlEntry
 * @package Bee\Security\Acls\Impl
 */
class AccessControlEntry implements IAccessControlEntry, IAuditableAccessControlEntry {

    /**
     * @var IAcl
     */
    private $acl;

    /**
     * @var IPermission
     */
    private $permission;

    /**
     * @var mixed
     */
    private $id;

    /**
     * @var ISid
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

	/**
	 * @param $id
	 * @param IAcl $acl
	 * @param ISid $sid
	 * @param IPermission $permission
	 * @param $granting
	 * @param $auditSuccess
	 * @param $auditFailure
	 */
    public function __construct($id, IAcl $acl, ISid $sid, IPermission $permission, $granting, $auditSuccess, $auditFailure) {
        Assert::notNull($acl, 'Acl required');
        Assert::notNull($sid, 'Sid required');
        Assert::notNull($permission, 'Permission required');
        $this->id = $id;
        $this->acl = $acl; // can be null
        $this->sid = $sid;
        $this->permission = $permission;
        $this->granting = (bool) $granting;
        $this->auditSuccess = $auditSuccess;
        $this->auditFailure = $auditFailure;
    }

	/**
	 * @return IAcl
	 */
    public function getAcl() {
        return $this->acl;
    }

	/**
	 * @param IAcl $acl
	 */
    public function setAcl(IAcl $acl) {
        $this->acl = $acl;
    }

	/**
	 * @return mixed
	 */
    public function getId() {
        return $this->id;
    }

	/**
	 * @return IPermission
	 */
    public function getPermission() {
        return $this->permission;
    }

	/**
	 * @param $permission
	 */
    public function setPermission($permission) {
        $this->permission = $permission;
    }

	/**
	 * @return ISid
	 */
    public function getSid() {
        return $this->sid;
    }

	/**
	 * @return bool
	 */
    public function isGranting() {
        return $this->granting;
    }

	/**
	 * @return bool
	 */
    public function isAuditFailure() {
        return $this->auditFailure;
    }

	/**
	 * @return bool
	 */
    public function isAuditSuccess() {
        return $this->auditSuccess;
    }
}
