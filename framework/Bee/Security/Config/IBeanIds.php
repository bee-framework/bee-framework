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
 * Created by IntelliJ IDEA.
 * User: mp
 * Date: Feb 19, 2010
 * Time: 5:18:14 PM
 * To change this template use File | Settings | File Templates.
 */

interface Bee_Security_Config_IBeanIds {
    const ACCESS_MANAGER = '_accessManager';
    const AFTER_INVOCATION_MANAGER = '_afterInvocationManager';	
    const AUTHENTICATION_MANAGER = '_authenticationManager';
    const DELEGATING_METHOD_DEFINITION_SOURCE = '_delegatingMethodDefinitionSource';
    const METHOD_DEFINITION_SOURCE_ADVISOR = '_methodDefinitionSourceAdvisor';
    const METHOD_SECURITY_INTERCEPTOR = '_methodSecurityInterceptor';
    const METHOD_SECURITY_INTERCEPTOR_POST_PROCESSOR = '_methodSecurityInterceptorPostProcessor';	
}
?>