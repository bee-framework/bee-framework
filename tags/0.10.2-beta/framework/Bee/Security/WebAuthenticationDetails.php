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
use Bee\MVC\IHttpRequest;
use Bee\Security\Concurrent\ISessionIdentifierAware;

/**
 * Created by IntelliJ IDEA.
 * User: mp
 * Date: Apr 23, 2010
 * Time: 10:02:43 PM
 */
class WebAuthenticationDetails implements ISessionIdentifierAware {

    private $remoteAddress;
    private $sessionId;

    //~ Constructors ===================================================================================================

    /**
     * Records the remote address and will also set the session Id if a session
     * already exists (it won't create one).
     *
     * @param IHttpRequest $request that the authentication request was received from
     */
    public function __construct(IHttpRequest $request) {
        $this->remoteAddress = $_SERVER['REMOTE_ADDR'];

        $this->sessionId = session_id();

        $this->doPopulateAdditionalInformation($request);
    }

    //~ Methods ========================================================================================================

    /**
     * Provided so that subclasses can populate additional information.
     *
     * @param IHttpRequest $request that the authentication request was received from
     */
    protected function doPopulateAdditionalInformation(IHttpRequest $request) {}

    public function equals($obj) {
        if ($obj instanceof WebAuthenticationDetails) {

            if ($this->remoteAddress != $obj->getRemoteAddress()) {
                return false;
            }

            if ($this->sessionId != $obj->getSessionId()) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * Indicates the TCP/IP address the authentication request was received from.
     *
     * @return string the address
     */
    public function getRemoteAddress() {
        return $this->remoteAddress;
    }

    /**
     * Indicates the <code>HttpSession</code> id the authentication request was received from.
     *
     * @return string the session ID
     */
    public function getSessionId() {
        return $this->sessionId;
    }

    public function __toString() {
        return 'WebAuthenticationDetails[RemoteIpAddress='.$this->getRemoteAddress().';SessionId='.$this->getSessionId().']';
    }
}