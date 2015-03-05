<?php
namespace Bee\Security\UserDetails;
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

use Bee\Security\IPasswordEncoder;
use Bee\Security\Provider\ISaltSource;
use Bee\Security\UserDetails\Doctrine2\UserBase;
use Bee\Utils\Strings;
use Exception;

/**
 * Class UserManagerBase
 * @package Bee\Security\UserDetails
 */
abstract class UserManagerBase {

	/**
	 * Enter description here...
	 *
	 * @var IPasswordEncoder
	 */
	private $passwordEncoder;

	/**
	 * Enter description here...
	 *
	 * @var ISaltSource
	 */
	private $saltSource = null;

	/**
	 * @param IPasswordEncoder $passwordEncoder
	 */
	public function setPasswordEncoder(IPasswordEncoder $passwordEncoder) {
		$this->passwordEncoder = $passwordEncoder;
	}

	/**
	 * @return IPasswordEncoder
	 */
	public function getPasswordEncoder() {
		return $this->passwordEncoder;
	}

	/**
	 * @param ISaltSource $saltSource
	 */
	public function setSaltSource(ISaltSource $saltSource) {
		$this->saltSource = $saltSource;
	}

	/**
	 * @return ISaltSource
	 */
	public function getSaltSource() {
		return $this->saltSource;
	}

	/**
	 * @param array $frmdata
	 * @return UserBase
	 * @throws Exception
	 */
	public function createOrUpdateUser(array $frmdata) {
		return $this->updateUser($this->findOrCreateUser($frmdata), $frmdata);
	}

	/**
	 * @param array $frmdata
	 * @return UserBase
	 */
	protected function findOrCreateUser(array $frmdata) {
		return is_numeric($frmdata['id']) ? $this->loadById($frmdata['id']) : $this->createUserInstance();
	}

	/**
	 * @param UserBase $user
	 * @param array $frmdata
	 * @return UserBase
	 * @throws PasswordConfirmationMismatchException
	 */
	protected function updateUser(UserBase $user, array $frmdata) {
		$user->setUsername($frmdata['username']);
		$user->setName($frmdata['fullname']);
		if (Strings::hasText($frmdata['password'])) {
			if ($frmdata['password'] !== $frmdata['password2']) {
				throw new PasswordConfirmationMismatchException("Passwords do not match!");
			}
			$this->setPassword($user, $frmdata['password']);
		}
		$user->setDisabled(filter_var($frmdata['deactivated'], FILTER_VALIDATE_BOOLEAN));
		$this->setRoles($frmdata, $user);
		return $this->addUser($user);
	}

	/**
	 * @param UserBase $user
	 * @param $password
	 */
	public function setPassword(UserBase $user, $password) {
		$user->setPassword($this->getPasswordEncoder()->encodePassword($password, $this->getSaltSource()->getSalt($user)));
	}

	/**
	 * @param int $length
	 * @return string
	 */
	public function getRandomPassword($length = 8) {
	    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
	    $pass = '';
	    $alphaLength = strlen($alphabet) - 1;
	    for ($i = 0; $i < $length; $i++) {
	        $n = rand(0, $alphaLength);
	        $pass .= $alphabet[$n];
	    }
	    return $pass;
	}

	/**
	 * @param $id
	 * @return UserBase
	 */
	abstract public function loadById($id);

	/**
	 * @param array $frmdata
	 * @param UserBase $user
	 * @return UserBase
	 */
	abstract public function setRoles(array $frmdata, UserBase $user);

	/**
	 * @param UserBase $user
	 * @return UserBase
	 */
	abstract public function addUser(UserBase $user);

	/**
	 * @return UserBase
	 */
	abstract public function createUserInstance();
} 