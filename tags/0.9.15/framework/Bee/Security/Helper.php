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
 * Date: Mar 23, 2010
 * Time: 11:11:15 AM
 * To change this template use File | Settings | File Templates.
 */
class Bee_Security_Helper {

	/**
	 * @var Bee_Security_IAccessDecisionManager
	 */
	private static $accessDecisionManager;

	/**
	 * @var Bee_Security_IAfterInvocationManager
	 */
	private static $afterInvocationProviderManager;

	/**
	 * @var Bee_Security_IUserDetailsService
	 */
	private static $userDetailsService;

	public static function construct(Bee_Security_IAccessDecisionManager $accessDecisionManager = null,
									 Bee_Security_IAfterInvocationManager $afterInvocationProviderManager = null,
									 Bee_Security_IUserDetailsService $userDetailsService = null) {
		self::$accessDecisionManager = $accessDecisionManager;
		self::$afterInvocationProviderManager = $afterInvocationProviderManager;
		self::$userDetailsService = $userDetailsService;
	}

	/**
	 * @return bool
	 */
	public static function isAuthenticated() {
		$auth = Bee_Security_Context_Holder::getContext()->getAuthentication();
		return is_null($auth) ? false : $auth->isAuthenticated();
	}

	/**
	 * @return array
	 */
	public static function getRoles() {
		$auth = Bee_Security_Context_Holder::getContext()->getAuthentication();
		if (is_null($auth) || !$auth->isAuthenticated()) {
			return array();
		}
		return (array_keys($auth->getAuthorities()));
	}

	/**
	 * @param $role
	 * @return bool
	 * @throws Bee_Security_Exception_Authentication
	 */
	public static function checkRole($role) {
		self::$accessDecisionManager->decide(
			self::getAuthIfAuthenticated(), null,
			new Bee_Security_ConfigAttributeDefinition($role)
		);
		return true;
	}

	/**
	 * @param $configAttribute
	 * @param null $secureObject
	 * @return bool
	 * @throws Bee_Security_Exception_Authentication
	 */
	public static function checkAccess($configAttribute, $secureObject = null) {
		self::$accessDecisionManager->decide(
			self::getAuthIfAuthenticated(), $secureObject,
			new Bee_Security_ConfigAttributeDefinition($configAttribute)
		);
		return true;
	}

	/**
	 * @param $configAttribute
	 * @param null $secureObject
	 * @param null $returnedObject
	 * @return mixed
	 * @throws Bee_Security_Exception_Authentication
	 */
	public static function checkResultAccess($configAttribute, $secureObject = null, $returnedObject = null) {
		return self::$afterInvocationProviderManager->decide(
			self::getAuthIfAuthenticated(), $secureObject,
			new Bee_Security_ConfigAttributeDefinition($configAttribute), $returnedObject
		);
	}

	/**
	 * @return mixed
	 */
	public static function getPrincipal() {
		return self::getAuthIfAuthenticated()->getPrincipal();
	}

	private static function getAuthIfAuthenticated() {
		$auth = Bee_Security_Context_Holder::getContext()->getAuthentication();
		if (is_null($auth) || !$auth->isAuthenticated()) {
			throw new Bee_Security_Exception_Authentication('Not authenticated');
		}
		return $auth;
	}

	/**
	 * @param $identityName
	 * @return mixed
	 * @throws Bee_Security_Exception_Authentication
	 */
	public static function getIdentity($identityName) {
		$auth = self::$userDetailsService->getGroupByName($identityName);
		if ($auth instanceof Potiscom_Auth_Doctrine_Group) {
			return $auth;
		}
		$auth = self::$userDetailsService->getUserByName($identityName);
		if ($auth instanceof Potiscom_Auth_Doctrine_User) {
			return $auth;
		}
		throw new Bee_Security_Exception_Authentication('Not authenticated');
	}

	/**
	 * @param $identityName
	 * @param $configAttribute
	 * @param null $secureObject
	 * @return bool
	 */
	public static function checkAccessForIdentity($identityName, $configAttribute, $secureObject = null) {
		$identity = self::getIdentity($identityName);
		$auth = new Bee_Security_UsernamePasswordAuthenticationToken($username, $password);
		self::$accessDecisionManager->decide($auth, $secureObject, new Bee_Security_ConfigAttributeDefinition($configAttribute));
		return true;
	}

	/**
	 * @param $identityName
	 * @param $configAttribute
	 * @param null $secureObject
	 * @param null $returnedObject
	 * @return mixed
	 */
	public static function checkResultAccessForIdentity($identityName, $configAttribute, $secureObject = null, $returnedObject = null) {
		$auth = self::getIdentity($identityName);
		return self::$afterInvocationProviderManager->decide($auth, $secureObject, new Bee_Security_ConfigAttributeDefinition($configAttribute), $returnedObject);
	}

}

class SEC extends Bee_Security_Helper {
}

?>