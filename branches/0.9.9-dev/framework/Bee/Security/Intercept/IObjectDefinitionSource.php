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
use Bee\Security\ConfigAttributeDefinition;

/**
 * Created by IntelliJ IDEA.
 * User: mp
 * Date: Feb 19, 2010
 * Time: 7:36:32 PM
 * To change this template use File | Settings | File Templates.
 */

interface Bee_Security_Intercept_IObjectDefinitionSource {

    /**
     * Accesses the <code>ConfigAttributeDefinition</code> that applies to a given secure object.<P>Returns
     * <code>null</code> if no <code>ConfigAttribiteDefinition</code> applies.</p>
     *
     * @param mixed $object the object being secured
     *
     * @return ConfigAttributeDefinition the <code>ConfigAttributeDefinition</code> that applies to the passed object
     *
     * @throws InvalidArgumentException if the passed object is not of a type supported by the
     *         <code>ObjectDefinitionSource</code> implementation
     */
    public function getAttributes($object);

    /**
     * If available, returns all of the <code>ConfigAttributeDefinition</code>s defined by the implementing class.
     * <p>
     * This is used by the {@link AbstractSecurityInterceptor} to perform startup time validation of each
     * <code>ConfigAttribute</code> configured against it.
     *
     * @return ConfigAttributeDefinition[] the <code>ConfigAttributeDefinition</code>s or <code>null</code> if unsupported
     */
    public function getConfigAttributeDefinitions(); // todo: do we need this in PHP?

    /**
     * Indicates whether the <code>ObjectDefinitionSource</code> implementation is able to provide
     * <code>ConfigAttributeDefinition</code>s for the indicated secure object type.
     *
     * @param ReflectionClass|string $classOrClassName the class that is being queried
     *
     * @return boolean true if the implementation can process the indicated class
     */
    public function supports($classOrClassName);
}
?>