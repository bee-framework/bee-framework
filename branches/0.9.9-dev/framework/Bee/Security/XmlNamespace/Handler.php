<?php
namespace Bee\Security\XmlNamespace;
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
use Bee\Context\Xml\XmlNamespace\HandlerSupport;
use Bee\Security\Elements;

/**
 * Created by IntelliJ IDEA.
 * User: mp
 * Date: Feb 17, 2010
 * Time: 6:35:32 PM
 * To change this template use File | Settings | File Templates.
 */

class Handler extends HandlerSupport {

    const INTERCEPT_METHODS = "intercept-methods";
    const GLOBAL_METHOD_SECURITY = 'global-method-security';

    public function init() {
//        $this->registerBeanDefinitionParser(Elements.LDAP_PROVIDER, new LdapProviderBeanDefinitionParser());
//        $this->registerBeanDefinitionParser(Elements.LDAP_SERVER, new LdapServerBeanDefinitionParser());
//        $this->registerBeanDefinitionParser(Elements.LDAP_USER_SERVICE, new LdapUserServiceBeanDefinitionParser());
		$this->registerBeanDefinitionParser(Elements::HTTP, new HttpSecurityBeanDefinitionParser());
//        $this->registerBeanDefinitionParser(Elements.USER_SERVICE, new UserServiceBeanDefinitionParser());
//        $this->registerBeanDefinitionParser(Elements.JDBC_USER_SERVICE, new JdbcUserServiceBeanDefinitionParser());
//        $this->registerBeanDefinitionParser(Elements.AUTHENTICATION_PROVIDER, new AuthenticationProviderBeanDefinitionParser());
        $this->registerBeanDefinitionParser(self::GLOBAL_METHOD_SECURITY, new GlobalMethodSecurityBeanDefinitionParser());
//        $this->registerBeanDefinitionParser(Elements.AUTHENTICATION_MANAGER, new AuthenticationManagerBeanDefinitionParser());
//        $this->registerBeanDefinitionParser(Elements.FILTER_INVOCATION_DEFINITION_SOURCE, new FilterInvocationDefinitionSourceBeanDefinitionParser());

        // Decorators
//        $this->registerBeanDefinitionDecorator(self::INTERCEPT_METHODS, new Bee_Security_Namespace_InterceptMethodsBeanDefinitionDecorator());
//        $this->registerBeanDefinitionDecorator(Elements.FILTER_CHAIN_MAP, new FilterChainMapBeanDefinitionDecorator());
//        $this->registerBeanDefinitionDecorator(Elements.CUSTOM_FILTER, new OrderedFilterBeanDefinitionDecorator());
//        $this->registerBeanDefinitionDecorator(Elements.CUSTOM_AUTH_PROVIDER, new CustomAuthenticationProviderBeanDefinitionDecorator());
//        $this->registerBeanDefinitionDecorator(Elements.CUSTOM_AFTER_INVOCATION_PROVIDER, new CustomAfterInvocationProviderBeanDefinitionDecorator());
    }
}
?>