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
use Bee\MVC\IFilter;
use Bee\MVC\IFilterChain;

/**
 * Created by IntelliJ IDEA.
 * User: mp
 * Date: Apr 23, 2010
 * Time: 9:28:54 PM
 * To change this template use File | Settings | File Templates.
 */

class Bee_Security_Anonymous_Filter implements IFilter {

    /**
     * @var string
     */
    private $key;
    /**
     * @var string
     */
    private $anonymousPrincipal;
    /**
     * @var string
     */
    private $anonymousAuthorities;
    /**
     * @var bool
     */
    private $removeAfterRequest = true;

    /**
     * Enables subclasses to determine whether or not an anonymous authentication token should be setup for
     * this request. This is useful if anonymous authentication should be allowed only for specific IP subnet ranges
     * etc.
     *
     * @param Bee_MVC_IHttpRequest $request to assist the method determine request details
     *
     * @return <code>true</code> if the anonymous token should be setup for this request (provided that the request
     *         doesn't already have some other <code>Authentication</code> inside it), or <code>false</code> if no
     *         anonymous token should be setup for this request
     */
    protected function applyAnonymousForThisRequest(Bee_MVC_IHttpRequest $request) {
        return true;
    }

    /**
     * @param Bee_MVC_IHttpRequest $request
     * @return Bee_Security_Anonymous_AuthenticationToken
     */
    protected function createAuthentication(Bee_MVC_IHttpRequest $request) {
        $auth = new Bee_Security_Anonymous_AuthenticationToken($this->key, $this->anonymousPrincipal, $this->anonymousAuthorities);
        $auth->setDetails(new Bee_Security_WebAuthenticationDetails($request));
        return $auth;
    }

    public function doFilter(Bee_MVC_IHttpRequest $request, IFilterChain $filterChain) {
        $addedToken = false;

        if ($this->applyAnonymousForThisRequest($request)) {

            $auth = Bee_Security_Context_Holder::getContext()->getAuthentication();

            $doLogin = false;
            if (is_null($auth)) {
                $doLogin = true;

            } else if (!$auth->isAuthenticated()) {
                $doLogin = true;
                
            } else {

            }

            if ($doLogin) {
                Bee_Security_Context_Holder::getContext()->setAuthentication($this->createAuthentication($request));
                $addedToken = true;

                Bee_Utils_Logger::debug('Populated SecurityContextHolder with anonymous token: '
                    . Bee_Security_Context_Holder::getContext()->getAuthentication());
            } else {
                Bee_Utils_Logger::debug('SecurityContextHolder not populated with anonymous token, as it already contained: '
                    . Bee_Security_Context_Holder::getContext()->getAuthentication());
            }
//            if (is_null(Bee_Security_Context_Holder::getContext()->getAuthentication())) {
//                Bee_Security_Context_Holder::getContext()->setAuthentication($this->createAuthentication($request));
//                $addedToken = true;
//
//                    Bee_Utils_Logger::debug('Populated SecurityContextHolder with anonymous token: '
//                        . Bee_Security_Context_Holder::getContext()->getAuthentication());
//            } else {
//                    Bee_Utils_Logger::debug('SecurityContextHolder not populated with anonymous token, as it already contained: '
//                        . Bee_Security_Context_Holder::getContext()->getAuthentication());
//            }
        }

        try {
            $filterChain->doFilter($request);
        } catch (Exception $e) {
            if ($addedToken && $this->removeAfterRequest
                && $this->createAuthentication($request)->equals(Bee_Security_Context_Holder::getContext()->getAuthentication())) {
                Bee_Security_Context_Holder::getContext()->setAuthentication(null);
            }
            throw $e;
        }
    }

    /**
     * Gets the Key
     *
     * @return string $key
     */
    public function getKey() {
        return $this->key;
    }

    /**
     * Sets the Key
     *
     * @param $key string
     * @return void
     */
    public function setKey($key) {
        $this->key = $key;
    }

    /**
     * Gets the RemoveAfterRequest
     *
     * @return  $removeAfterRequest
     */
    public function getRemoveAfterRequest() {
        return $this->removeAfterRequest;
    }

    /**
     * Controls whether the filter will remove the Anonymous token after the request is complete. Generally
     * this is desired to avoid the expense of a session being created by {@link
     * org.springframework.security.context.HttpSessionContextIntegrationFilter HttpSessionContextIntegrationFilter} simply to
     * store the Anonymous authentication token.<p>Defaults to <code>true</code>, being the most optimal and
     * appropriate option (ie <code>AnonymousProcessingFilter</code> will clear the token at the end of each request,
     * thus avoiding the session creation overhead in a typical configuration.</p>
     *
     * @param $removeAfterRequest
     * @return void
     */
    public function setRemoveAfterRequest( $removeAfterRequest) {
        $this->removeAfterRequest = $removeAfterRequest;
    }

    /**
     * Gets the AnonymousPrincipal
     *
     * @return string $anonymousPrincipal
     */
    public function getAnonymousPrincipal() {
        return $this->anonymousPrincipal;
    }

    /**
     * Sets the AnonymousPrincipal
     *
     * @param $anonymousPrincipal string
     * @return void
     */
    public function setAnonymousPrincipal($anonymousPrincipal) {
        $this->anonymousPrincipal = $anonymousPrincipal;
    }

    /**
     * Gets the AnonymousAuthorities
     *
     * @return array $anonymousAuthorities
     */
    public function getAnonymousAuthorities() {
        return $this->anonymousAuthorities;
    }

    /**
     * Sets the AnonymousAuthorities
     *
     * @param $anonymousAuthorities array
     * @return void
     */
    public function setAnonymousAuthorities(array $anonymousAuthorities) {
        $this->anonymousAuthorities = $anonymousAuthorities;
    }

}
?>
