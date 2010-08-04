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
 * Date: Mar 18, 2010
 * Time: 1:34:49 AM
 */

interface Bee_Security_Acls_IPermission {

    const RESERVED_ON = '~';
    const RESERVED_OFF = '.';
    const THIRTY_TWO_RESERVED_OFF = '................................';

    //~ Methods ========================================================================================================

    /**
     * Returns the bits that represents the permission.
     *
     * @return the bits that represent the permission
     */
    public function getMask();

    /**
     * Returns a 32-character long bit pattern <code>String</code> representing this permission.
     * <p>
     * Implementations are free to format the pattern as they see fit, although under no circumstances may
     * {@link #RESERVED_OFF} or {@link #RESERVED_ON} be used within the pattern. An exemption is in the case of
     * {@link #RESERVED_OFF} which is used to denote a bit that is off (clear).
     * Implementations may also elect to use {@link #RESERVED_ON} internally for computation purposes,
     * although this method may not return any <code>String</code> containing {@link #RESERVED_ON}.
     * </p>
     *  <p>The returned String must be 32 characters in length.</p>
     *  <p>This method is only used for user interface and logging purposes. It is not used in any permission
     * calculations. Therefore, duplication of characters within the output is permitted.</p>
     *
     * @return a 32-character bit pattern
     */
    public function getPattern();
}
