<?php
namespace Bee\Security\UserDetails\Doctrine2;
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

use Bee\Security\UserDetails\UserManagerBase;
use Bee_Security_Exception_UsernameNotFound;
use Bee_Security_IUserDetails;
use Bee_Security_IUserDetailsService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;

/**
 * Class SimpleUserDetailsService
 * @package Bee\Security\UserDetails\Doctrine2
 */
class SimpleUserDetailsService extends UserManagerBase implements Bee_Security_IUserDetailsService {

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var string
	 */
	private $userEntityName = 'Bee\Security\UserDetails\Doctrine2\SimpleUser';

	/**
	 * @param EntityManager $entityManager
	 */
	public function setEntityManager(EntityManager $entityManager) {
		$this->entityManager = $entityManager;
	}

	/**
	 * @return EntityManager
	 */
	public function getEntityManager() {
		return $this->entityManager;
	}

	/**
	 * @param string $userEntityName
	 */
	public function setUserEntityName($userEntityName) {
		$this->userEntityName = $userEntityName;
	}

	/**
	 * @return string
	 */
	public function getUserEntityName() {
		return $this->userEntityName;
	}

	/**
	 * Locates the user based on the username. In the actual implementation, the search may possibly be case
	 * insensitive, or case insensitive depending on how the implementaion instance is configured. In this case, the
	 * <code>Bee_Security_IUserDetails</code> object that comes back may have a username that is of a different case
	 * than what was actually requested.
	 *
	 * @param $username
	 * @throws \Bee_Security_Exception_UsernameNotFound
	 * @return Bee_Security_IUserDetails a fully populated user record (never <code>null</code>)
	 */
	function loadUserByUsername($username) {
		try {
			return $this->getEntityManager()
					->createQuery('SELECT u FROM ' . $this->getUserEntityName() . ' u WHERE u.username = :username')
					->setParameter('username', $username)->getSingleResult();
		} catch (NoResultException $e) {
			throw new Bee_Security_Exception_UsernameNotFound('User name ' . $username . ' not found', null, $e);
		}
	}

	/**
	 * @return UserBase[]
	 */
	public function listUsers() {
		return $this->getEntityManager()->
				createQuery('SELECT u FROM ' . $this->getUserEntityName() . ' u ORDER BY u.username ASC')->execute();
	}

	/**
	 * @param $id
	 * @return null|UserBase
	 */
	public function loadById($id) {
		return $this->getEntityManager()->find($this->getUserEntityName(), $id);
	}

	/**
	 * @param UserBase $user
	 * @return UserBase
	 */
	public function addUser(UserBase $user) {
		$this->getEntityManager()->persist($user);
		$this->getEntityManager()->flush($user);
		return $user;
	}

	/**
	 * @param UserBase $user
	 */
	public function removeUser(UserBase $user) {
		$this->getEntityManager()->remove($user);
		$this->getEntityManager()->flush($user);
	}

	/**
	 * @param array $frmdata
	 * @param UserBase $user
	 * @return UserBase
	 */
	public function setRoles(array $frmdata, UserBase $user) {
		/** @var SimpleUser $user */
		if ($frmdata['admin']) {
			$user->addRole('ROLE_ADMINISTRATOR');
		} else {
			$user->removeRole('ROLE_ADMINISTRATOR');
		}
		$user->addRole('ROLE_USER');
		return $user;
	}

	/**
	 * @return UserBase
	 */
	public function createUserInstance() {
		return new $this->userEntityName();
	}
}