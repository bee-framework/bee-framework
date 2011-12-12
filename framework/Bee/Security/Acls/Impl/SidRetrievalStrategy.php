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
 * Date: Mar 22, 2010
 * Time: 11:34:21 PM
 */

class Bee_Security_Acls_Impl_SidRetrievalStrategy implements Bee_Security_Acls_ISidRetrievalStrategy {

    public function getSids(Bee_Security_IAuthentication $authentication) {
        $sids = array();

        $sids[] = new Bee_Security_Acls_Impl_PrincipalSid($authentication);

        $authorities = $authentication->getAuthorities();
        foreach($authorities as $authority => $dummy) {
            $sids[] = new Bee_Security_Acls_Impl_GrantedAuthoritySid($authority);
        }

        return $sids;
    }
}
?>
