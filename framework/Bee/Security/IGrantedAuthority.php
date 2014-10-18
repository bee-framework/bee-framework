<?php
namespace Bee\Security;
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
 * Represents an authority granted to an {@link IAuthentication} object.
 *
 * <p>
 * A <code>IGrantedAuthority</code> must either represent itself as a
 * <code>String</code> or be specifically supported by an  {@link
 * IAccessDecisionManager}.
 * </p>
 * 
 * <p>
 * Implementations must implement {@link Comparable} in order to ensure that
 * array sorting logic guaranteed by {@link UserDetails#getAuthorities()} can
 * be reliably implemented.  // TODO!!!
 * </p>
 *
 */
interface IGrantedAuthority { // extends Serializable, Comparable
	
	/**
	 * If the <code>IGrantedAuthority</code> can be represented as a <code>String</code> and that
     * <code>String</code> is sufficient in precision to be relied upon for an access control decision by an {@link
     * IAccessDecisionManager} (or delegate), this method should return such a <code>String</code>.<p>If the
     * <code>IGrantedAuthority</code> cannot be expressed with sufficient precision as a <code>String</code>,
     * <code>null</code> should be returned. Returning <code>null</code> will require an
     * <code>IAccessDecisionManager</code> (or delegate) to  specifically support the <code>IGrantedAuthority</code>
     * implementation, so returning <code>null</code> should be avoided unless actually  required.</p>
	 *
	 * @return String a representation of the granted authority (or <code>null</code> if the granted authority cannot be
     *         expressed as a <code>String</code> with sufficient precision).
	 */
	public function getAuthority();
}