<?php

namespace Bee\Security\UserDetails\Doctrine2;
use Bee_Security_IUserDetails;


/**
 * Class UserBase
 * @package Bee\Security\UserDetails\Doctrine2
 * @MappedSuperclass
 */
abstract class UserBase implements Bee_Security_IUserDetails {

	/**
	 * @var integer
	 *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="integer")
	 */
	private $id;

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
	 * Get the identifier
	 *
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

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