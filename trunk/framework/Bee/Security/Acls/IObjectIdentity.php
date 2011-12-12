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
 * Date: Mar 16, 2010
 * Time: 6:42:18 PM
 */

interface Bee_Security_Acls_IObjectIdentity {
    /**
     * Obtains the actual identifier. This identifier must not be reused to represent other domain objects with
     * the same <tt>javaType</tt>.
     *
     * <p>Because ACLs are largely immutable, it is strongly recommended to use
     * a synthetic identifier (such as a database sequence number for the primary key). Do not use an identifier with
     * business meaning, as that business meaning may change in the future such change will cascade to the ACL
     * subsystem data.</p>
     *
     * @return the identifier (unique within this <tt>type</tt>; never <tt>null</tt>)
     */
    public function getIdentifier();

    /**
     * Obtains the Java type represented by the domain object. The Java type can be an interface or a class, but is
     * most often the domain object implementation class.
     *
     * @return the type of the domain object (usually the class name, never <tt>null</tt>)
     */
    public function getType();

    /**
     * Used for associative array indexing. Kinda like Java's hashCode()...
     * @abstract
     * @return string
     */
    public function getIdentifierString();
}
?>
