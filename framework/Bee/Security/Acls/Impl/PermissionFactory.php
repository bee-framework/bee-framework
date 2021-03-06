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
use Bee\Security\Acls\IPermission;
use Bee\Security\Acls\IPermissionFactory;
use Bee\Utils\Assert;

/**
 * User: mp
 * Date: Mar 18, 2010
 * Time: 11:00:52 AM
 */

class PermissionFactory implements IPermissionFactory {

    /**
     * @var IPermission[]
     */
    private $registeredPermissions;

    /**
     * @param IPermission[] $registeredPermissions
     * @return void
     */
    public function setRegisteredPermissions(array $registeredPermissions) {
        $this->registeredPermissions = $registeredPermissions;

        foreach($this->registeredPermissions as $perm) {
            $this->registeredPermissions['_'.$perm->getMask()] = $perm;
        }
    }


    public function buildFromMask($mask) {
        if(array_key_exists('_'.$mask, $this->registeredPermissions)) {
            return $this->registeredPermissions['_'.$mask];
        }

        $permission = new CumulativePermission();

        for($i = 0; $i < 32; $i++) {
            $permToCheck = 1 << $i;

            if(($mask & $permToCheck) == $permToCheck) {
				Assert::isTrue(array_key_exists('_'.$permToCheck, $this->registeredPermissions), 'Mask '.
                        $permToCheck.' does not have a corresponding static Permission');
                $p = $this->registeredPermissions['_'.$permToCheck];
                $permission->set($p);
            }
        }

        $this->registeredPermissions['_'.$permission->getMask()] = $permission;

        return $permission;
    }

    public function buildFromName($name) {
		Assert::isTrue(array_key_exists($name, $this->registeredPermissions), 'Unknown permission '.$name);
        return $this->registeredPermissions[$name];
    }
}
