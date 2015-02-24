<?php
namespace Bee\Persistence\Doctrine2;

/**
 * Class GenericDao - provides generic CRUD/L operations for a given entity class. The entity class name must be
 * configured via the $entity property.
 *
 * @package Bee\Persistence\Doctrine2
 */
class SimpleDao extends GenericDaoBase {

	/**
	 * @var string
	 */
	private $entity;

	/**
	 * @return string|array
	 */
	protected function getIdFieldName() {
		$classMetadata = $this->getEntityManager()->getClassMetadata($this->getEntity());
		$idFields = $classMetadata->getIdentifierFieldNames();
		return count($idFields) > 1 ? $idFields : $idFields[0];
	}

	// =================================================================================================================
	// == CRUD operations : create / update ============================================================================
	// =================================================================================================================

	/**
	 * @param mixed $entity
	 * @param bool $flush
	 * @return mixed
	 */
	public function persist($entity, $flush = true) {
		$this->prePersist($entity);
		$this->getEntityManager()->persist($entity);
		$this->postPersist($entity);
		if ($flush) {
			$this->getEntityManager()->flush();
		}
		return $entity;
	}

	protected function prePersist($entity) {
	}

	protected function postPersist($entity) {
	}

	// =================================================================================================================
	// == CRUD operations : delete =====================================================================================
	// =================================================================================================================
	/**
	 * @param $entity
	 * @param bool $flush
	 * @return void
	 */
	public function delete($entity, $flush = true) {
		$this->preDelete($entity);
		$this->getEntityManager()->remove($entity);
		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

	protected function preDelete($entity) {
	}

	// =================================================================================================================
	// == GETTERS & SETTERS ============================================================================================
	// =================================================================================================================

	/**
	 * @return string
	 */
	public function getEntity() {
		return $this->entity;
	}

	/**
	 * @param string $entity
	 */
	public function setEntity($entity) {
		$this->entity = $entity;
	}
}