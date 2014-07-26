<?php
namespace Bee\Security\UserDetails\Doctrine2;
use \Bee_Security_IGrantedAuthority;


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
	 * @return Bee_Security_IGrantedAuthority[] the authorities, sorted by natural key (never <code>null</code>)
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

	public function removeRole($role) {
		$this->getAuthorities();
		unset($this->rolesTransformed[$role]);
		$this->updateRoles();
	}

	public function hasRole($role) {
		return array_key_exists($role, $this->getAuthorities());
	}

	private function updateRoles() {
		$this->roles = array_keys($this->rolesTransformed);
	}

} 