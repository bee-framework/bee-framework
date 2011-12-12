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
 * Date: Feb 18, 2010
 * Time: 7:51:57 AM
 */

class Bee_AOP_Framework_ProxyConfig {

    /**
     * @var boolean
     */
    private $proxyTargetClass = false;

    /**
     * @var boolean
     */
    protected $opaque = false;

    /**
     * @var boolean
     */
    protected $exposeProxy = false;

    /**
     * @var boolean
     */
    private $frozen = false;


    /**
     * Set whether to proxy the target class directly, instead of just proxying
     * specific interfaces. Default is "false".
     * <p>Set this to "true" to force proxying for the TargetSource's exposed
     * target class. If that target class is an interface, a JDK proxy will be
     * created for the given interface. If that target class is any other class,
     * a CGLIB proxy will be created for the given class.
     * <p>Note: Depending on the configuration of the concrete proxy factory,
     * the proxy-target-class behavior will also be applied if no interfaces
     * have been specified (and no interface autodetection is activated).
     * @see org.springframework.aop.TargetSource#getTargetClass()
     */
    public function setProxyTargetClass($proxyTargetClass) {
        $this->proxyTargetClass = $proxyTargetClass;
    }

    /**
     * Return whether to proxy the target class directly as well as any interfaces.
     */
    public function isProxyTargetClass() {
        return $this->proxyTargetClass;
    }

    /**
     * Set whether proxies created by this configuration should be prevented
     * from being cast to {@link Advised} to query proxy status.
     * <p>Default is "false", meaning that any AOP proxy can be cast to
     * {@link Advised}.
     */
    public function setOpaque($opaque) {
        $this->opaque = $opaque;
    }

    /**
     * Return whether proxies created by this configuration should be
     * prevented from being cast to {@link Advised}.
     */
    public function isOpaque() {
        return $this->opaque;
    }

    /**
     * Set whether the proxy should be exposed by the AOP framework as a
     * ThreadLocal for retrieval via the AopContext class. This is useful
     * if an advised object needs to call another advised method on itself.
     * (If it uses <code>this</code>, the invocation will not be advised).
     * <p>Default is "false", for optimal performance.
     */
    public function setExposeProxy($exposeProxy) {
        $this->exposeProxy = $exposeProxy;
    }

    /**
     * Return whether the AOP proxy will expose the AOP proxy for
     * each invocation.
     */
    public function isExposeProxy() {
        return $this->exposeProxy;
    }

    /**
     * Set whether this config should be frozen.
     * <p>When a config is frozen, no advice changes can be made. This is
     * useful for optimization, and useful when we don't want callers to
     * be able to manipulate configuration after casting to Advised.
     */
    public function setFrozen($frozen) {
        $this->frozen = $frozen;
    }

    /**
     * Return whether the config is frozen, and no advice changes can be made.
     */
    public function isFrozen() {
        return $this->frozen;
    }


    /**
     * Copy configuration from the other config object.
     * @param other object to copy configuration from
     */
    public function copyFrom(Bee_AOP_Framework_ProxyConfig $other) {
        $this->proxyTargetClass = $other->proxyTargetClass;
        $this->exposeProxy = $other->exposeProxy;
        $this->frozen = $other->frozen;
        $this->opaque = $other->opaque;
    }

}

?>