<?php
namespace Bee\Security\UserDetails\Doctrine2;
use Bee_Security_Exception_UsernameNotFound;
use Bee_Security_IUserDetails;
use Bee_Security_IUserDetailsService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;

/**
 * Class SimpleUserDetailsService
 * @package Bee\Security\UserDetails\Doctrine2
 */
class SimpleUserDetailsService implements Bee_Security_IUserDetailsService {

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
	 */
	public function addUser(UserBase $user) {
		$this->getEntityManager()->persist($user);
		$this->getEntityManager()->flush($user);
	}

	/**
	 * @param UserBase $user
	 */
	public function removeUser(UserBase $user) {
		$this->getEntityManager()->remove($user);
		$this->getEntityManager()->flush($user);
	}
}