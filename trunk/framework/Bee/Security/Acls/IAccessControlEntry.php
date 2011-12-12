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
 * Date: Feb 13, 2010
 * Time: 6:35:49 PM
 */

interface Bee_Security_Acls_IAccessControlEntry {

    //~ Methods ========================================================================================================

    /**
     * @abstract
     * @return Bee_Security_Acls_IAcl
     */
    public function getAcl();

    /**
     * Obtains an identifier that represents this ACE.
     *
     * @return mixed the identifier, or <code>null</code> if unsaved
     */
    public function getId();

    /**
     * @abstract
     * @return Bee_Security_Acls_IPermission
     */
    public function getPermission();

    /**
     * @abstract
     * @return Bee_Security_Acls_ISid
     */
    public function getSid();

    /**
     * Indicates the a Permission is being granted to the relevant Sid. If false, indicates the permission is
     * being revoked/blocked.
     *
     * @return bool true if being granted, false otherwise
     */
    public function isGranting();
}
?>
