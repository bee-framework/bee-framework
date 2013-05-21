<?php
namespace Bee\Persistence\Doctrine\Behaviors;
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
use Bee\Persistence\Behaviors\AbstractFieldBasedDelegate;
use Bee\Persistence\Pdo\FeatureDetector;
use Bee\Persistence\Pdo\Utils;

/**
 * User: mp
 * Date: 20.05.13
 * Time: 02:19
 */
 
class DelegateBase extends AbstractFieldBasedDelegate {

	/**
	 * @var \Doctrine_Connection
	 */
	private $doctrineConnection;

	/**
	 * @param string $entityClass
	 * @param \Doctrine_Connection $doctrineConnection
	 * @return \Bee\Persistence\Doctrine\Behaviors\DelegateBase
	 */
	public function __construct($entityClass, \Doctrine_Connection $doctrineConnection) {
		parent::__construct($entityClass);
		$this->doctrineConnection = $doctrineConnection;
	}

	/**
	 * @return \Doctrine_Connection
	 */
	public function getDoctrineConnection() {
		return $this->doctrineConnection;
	}

	/**
	 * @return \Doctrine_Query
	 */
	protected function getEntityBaseQuery() {
		return $this->getDoctrineConnection()->createQuery()->from($this->getQueryDomain());
	}

	/**
	 * @return \Doctrine_Query
	 */
	protected function getEntityUpdateBaseQuery() {
		return $this->getDoctrineConnection()->createQuery()->update($this->getQueryDomain());
	}

	/**
	 * @param \Doctrine_Query $qry
	 * @param mixed $orderedEntity
	 * @throws \Exception
	 * @return \Doctrine_Query
	 */
	protected function addIdentityRestriction(\Doctrine_Query $qry, $orderedEntity) {
		if($orderedEntity instanceof \Doctrine_Record) {
			$id = $orderedEntity->get($this->getIdFieldName());
		} else if(is_numeric($orderedEntity)) {
			$id = $orderedEntity;
		} else {
			throw new \Exception('Unable to handle unknown identifier type');
		}
		return $qry->addWhere('id = :id', array(':id' => $id));
	}

	protected function addGroupRestriction(\Doctrine_Query $qry, $orderedEntity, $restriction = false) {
		if ($this->getGroupFieldName()) {
			if ($restriction === false) {
				$restriction = $this->getGroup($orderedEntity);
			}
			$this->doAddGroupRestriction($qry, $restriction);
		}
		return $qry;
	}

	protected function doAddGroupRestriction(\Doctrine_Query $qry, $restriction = false) {
		return $qry->addWhere($this->getGroupFieldName().' = :grp_id', array(':grp_id' => $restriction));
	}

	/**
	 * @param string $feature
	 * @return bool
	 */
	protected function pdoSupportsFeature($feature) {
		return FeatureDetector::supports($feature, $this->getDoctrineConnection()->getDbh());
	}

	/**
	 * @param $entity
	 * @return mixed
	 */
	protected function getGroup($entity) {
		$grpQry = $this->getDoctrineConnection()->createQuery()->select($this->getGroupFieldName())->from($this->getQueryDomain());
		return $this->addIdentityRestriction($grpQry, $entity)->fetchOne(array(), \Doctrine_Core::HYDRATE_SINGLE_SCALAR);
	}
}
