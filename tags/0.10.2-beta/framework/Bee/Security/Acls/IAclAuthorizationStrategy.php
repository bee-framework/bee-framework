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

/**
 * User: mp
 * Date: Mar 18, 2010
 * Time: 1:02:34 AM
 */

interface IAclAuthorizationStrategy {

    const CHANGE_OWNERSHIP = 0;
    const CHANGE_AUDITING = 1;
    const CHANGE_GENERAL = 2;

    //~ Methods ========================================================================================================

    public function securityCheck(IAcl $acl, $changeType);
}