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
use Bee\Security\IUserDetails;
use Bee\Tools\Entities\Persistence\Doctrine2\TAutoIncIdentity;

/**
 * Class UserBase
 * @package Bee\Security\UserDetails\Doctrine2
 * @MappedSuperclass
 */
abstract class UserBase implements IUserDetails {
    use TAutoIncIdentity;

	/**
	 * @var string
	 * @Column(name="username", type="string", length=150, nullable=false, unique=true)
	 */
	private $username;

	/**
	 * @var string
	 * @Column(name="password", type="string", length=20, nullable=false)
	 */
	private $password;

	/**
	 * @var boolean
	 * @Column(name="disabled", type="boolean", nullable=false)
	 */
	protected $disabled = false;

	/**
	 * @var string
	 * @Column(name="name", type="string", length=200, nullable=true)
	 */
	protected $name;

	/**
	 * Returns the password used to authenticate the user. Cannot return <code>null</code>.
	 *
	 * @return String the password (never <code>null</code>)
	 */
	function getPassword() {
		return $this->password;
	}

	/**
	 * @param string $password
	 */
	public function setPassword($password) {
		$this->password = $password;
	}

	/**
	 * Returns the username used to authenticate the user. Cannot return <code>null</code>.
	 *
	 * @return String the username (never <code>null</code>)
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * @param string $username
	 */
	public function setUsername($username) {
		$this->username = $username;
	}

	/**
	 * Indicates whether the user's account has expired. An expired account cannot be authenticated.
	 *
	 * @return boolean <code>true</code> if the user's account is valid (ie non-expired), <code>false</code> if no longer valid
	 *         (ie expired)
	 */
	public function isAccountNonExpired() {
		return true;
	}

	/**
	 * Indicates whether the user is locked or unlocked. A locked user cannot be authenticated.
	 *
	 * @return boolean <code>true</code> if the user is not locked, <code>false</code> otherwise
	 */
	public function isAccountNonLocked() {
		return true;
	}

	/**
	 * Indicates whether the user's credentials (password) has expired. Expired credentials prevent
	 * authentication.
	 *
	 * @return boolean <code>true</code> if the user's credentials are valid (ie non-expired), <code>false</code> if no longer
	 *         valid (ie expired)
	 */
	public function isCredentialsNonExpired() {
		return true;
	}

	/**
	 * Indicates whether the user is enabled or disabled. A disabled user cannot be authenticated.
	 *
	 * @return boolean <code>true</code> if the user is enabled, <code>false</code> otherwise
	 */
	public function isEnabled() {
		return !$this->getDisabled();
	}

	/**
	 * @return boolean
	 */
	public function getDisabled() {
		return $this->disabled;
	}

	/**
	 * @param boolean $disabled
	 */
	public function setDisabled($disabled) {
		$this->disabled = $disabled;
	}

	public function __toString() {
		return $this->getUsername();
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name ? $this->name : $this->getUsername();
	}
}