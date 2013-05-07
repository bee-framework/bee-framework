<?php
namespace Bee\Persistence\Doctrine2;

use \Doctrine\ORM\EntityManager;

/**
 * User: mp
 * Date: 05.05.13
 * Time: 17:26
 */
class DaoBase {

	/**
	 * @var EntityManager
	 */
	private $em;

	/**
	 * @return EntityManager
	 */
	public function getEm() {
		return $this->em;
	}

	/**
	 * @param EntityManager $em
	 */
	public function setEm(EntityManager $em) {
		$this->em = $em;
	}

}
