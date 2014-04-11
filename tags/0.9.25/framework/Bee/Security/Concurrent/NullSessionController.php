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
 * No-op implementation of {@link Bee_Security_Concurrent_ISessionController}.
 *
 */
class Bee_Security_Concurrent_NullSessionController implements Bee_Security_Concurrent_ISessionController {
    public function checkAuthenticationAllowed(Bee_Security_IAuthentication $request) {}
    public function registerSuccessfulAuthentication(Bee_Security_IAuthentication $authentication) {}
}
?>