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
use Bee\Security\Context\SecurityContextHolder;
use Bee\Security\Exception\AuthenticationException;

/**
 * Class Helper
 * @package Bee\Security
 */
class Helper {

	/**
	 * @var IAccessDecisionManager
	 */
	private static $accessDecisionManager;

	/**
	 * @var IAfterInvocationManager
	 */
	private static $afterInvocationProviderManager;

	/**
	 * @var IUserDetailsService
	 */
	private static $userDetailsService;

	public static function construct(IAccessDecisionManager $accessDecisionManager = null,
									 IAfterInvocationManager $afterInvocationProviderManager = null,
									 IUserDetailsService $userDetailsService = null) {
		self::$accessDecisionManager = $accessDecisionManager;
		self::$afterInvocationProviderManager = $afterInvocationProviderManager;
		self::$userDetailsService = $userDetailsService;
	}

	/**
	 * @return bool
	 */
	public static function isAuthenticated() {
		$auth = SecurityContextHolder::getContext()->getAuthentication();
		return is_null($auth) ? false : $auth->isAuthenticated();
	}

	/**
	 * @return array
	 */
	public static function getRoles() {
		$auth = SecurityContextHolder::getContext()->getAuthentication();
		if (is_null($auth) || !$auth->isAuthenticated()) {
			return array();
		}
		return (array_keys($auth->getAuthorities()));
	}

	/**
	 * @param $role
	 * @return bool
	 * @throws AuthenticationException
	 */
	public static function checkRole($role) {
		self::$accessDecisionManager->decide(
			self::getAuthIfAuthenticated(), null,
			new ConfigAttributeDefinition($role)
		);
		return true;
	}

	/**
	 * @param $configAttribute
	 * @param null $secureObject
	 * @return bool
	 * @throws AuthenticationException
	 */
	public static function checkAccess($configAttribute, $secureObject = null) {
		self::$accessDecisionManager->decide(
			self::getAuthIfAuthenticated(), $secureObject,
			new ConfigAttributeDefinition($configAttribute)
		);
		return true;
	}

	/**
	 * @param $configAttribute
	 * @param null $secureObject
	 * @param null $returnedObject
	 * @return mixed
	 * @throws AuthenticationException
	 */
	public static function checkResultAccess($configAttribute, $secureObject = null, $returnedObject = null) {
		return self::$afterInvocationProviderManager->decide(
			self::getAuthIfAuthenticated(), $secureObject,
			new ConfigAttributeDefinition($configAttribute), $returnedObject
		);
	}

	/**
	 * @return mixed
	 */
	public static function getPrincipal() {
		return self::getAuthIfAuthenticated()->getPrincipal();
	}

	/**
	 * @return IAuthentication
	 * @throws AuthenticationException
	 */
	private static function getAuthIfAuthenticated() {
		$auth = SecurityContextHolder::getContext()->getAuthentication();
		if (is_null($auth) || !$auth->isAuthenticated()) {
			throw new AuthenticationException('Not authenticated');
		}
		return $auth;
	}
}

class SEC extends Helper {
}