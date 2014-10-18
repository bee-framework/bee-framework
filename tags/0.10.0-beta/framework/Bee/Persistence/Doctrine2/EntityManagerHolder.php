<?php
namespace Bee\Persistence\Doctrine2;
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
use \Doctrine\ORM\EntityManager;

/**
 * User: mp
 * Date: 27.06.13
 * Time: 04:17
 */
 
class EntityManagerHolder {

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
     * @return EntityManager $entityManager
	 */
    public function getEntityManager() {
        return $this->entityManager;
	}

	/**
     * @param $entityManager EntityManager
	 */
    public function setEntityManager(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
    }

	/**
	 * Convenience wrapper around EntityManager::transactional()
	 * @param $callback
	 * @return mixed
	 */
	public function transactional($callback) {
		$that = $this;
		return $this->getEntityManager()->transactional(function (EntityManager $em) use ($callback, $that) {
			return $callback($that);
		});
	}
}