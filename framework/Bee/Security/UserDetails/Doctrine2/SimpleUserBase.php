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
use Bee\Security\IGrantedAuthority;

/**
 * Base class for user entities
 * @package Bee\Security\UserDetails\Doctrine2
 * @MappedSuperclass
 */
class SimpleUserBase extends UserBase {
	/**
	 * @var array
	 * @Column(name="roles", type="simple_array", nullable=true)
	 */
	protected $roles = array();

	/**
	 * @var null|array
	 */
	private $rolesTransformed = null;

	/**
	 * Returns the authorities granted to the user. Cannot return <code>null</code>.
	 *
	 * todo: doesn't actually return IGrantedAuthority[] ... what gives?
	 * @return IGrantedAuthority[] the authorities, sorted by natural key (never <code>null</code>)
	 */
	public function getAuthorities() {
		if(!$this->rolesTransformed) {
			$this->rolesTransformed = array_fill_keys($this->roles, true);
		}
		return $this->rolesTransformed;
	}

	/**
	 * @param string $role
	 */
	public function addRole($role) {
		$this->getAuthorities();
		$this->rolesTransformed[$role] = true;
		$this->updateRoles();
	}

	/**
	 * @param $role
	 */
	public function removeRole($role) {
		$this->getAuthorities();
		unset($this->rolesTransformed[$role]);
		$this->updateRoles();
	}

	/**
	 * @param $role
	 * @return bool
	 */
	public function hasRole($role) {
		return array_key_exists($role, $this->getAuthorities());
	}

	/**
	 *
	 */
	private function updateRoles() {
		$this->roles = array_keys($this->rolesTransformed);
	}
} 