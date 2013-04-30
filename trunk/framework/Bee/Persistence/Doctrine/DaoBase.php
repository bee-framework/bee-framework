<?php
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

class Bee_Persistence_Doctrine_DaoBase {
	
	/**
	 * Enter description here...
	 *
	 * @var Doctrine_Connection
	 */
	private $doctrineConnection;
	
	/**
	 * Enter description here...
	 *
	 * @return Doctrine_Connection
	 */
	public final function getDoctrineConnection() {
		return $this->doctrineConnection;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param Doctrine_Connection $doctrineConnection
	 * @return void
	 */
	public final function setDoctrineConnection(Doctrine_Connection $doctrineConnection) {
		$this->doctrineConnection = $doctrineConnection;
	}

    /**
     * @param Doctrine_Query $query
     * @param array $params
     * @param Bee_Persistence_IRestrictionHolder $restrictionHolder
     * @param Bee_Persistence_IOrderAndLimitHolder $orderAndLimitHolder
     * @param array $defaultOrderMapping
     *
     * @return array
     */
    public function executeListQuery(Doctrine_Query $query, array $params=array(), Bee_Persistence_IRestrictionHolder $restrictionHolder = null, Bee_Persistence_IOrderAndLimitHolder $orderAndLimitHolder = null, array $defaultOrderMapping) {
        $this->applyFilterRestrictions($query, $params, $restrictionHolder);
        $this->applyOrderAndLimit($query, $params, $orderAndLimitHolder, $defaultOrderMapping);
        $result = $query->execute($params);
        return $result->getData();
    }

    /**
     * @param Doctrine_Query $query
     * @param array $params
     * @param Bee_Persistence_IRestrictionHolder $restrictionHolder
     */
    protected final function applyFilterRestrictions(Doctrine_Query &$query, array &$params, Bee_Persistence_IRestrictionHolder $restrictionHolder = null) {
        if (is_null($restrictionHolder)) {
            return;
        }

        if (!Bee_Utils_Strings::hasText($restrictionHolder->getFilterString())) {
            return;
        }

        /**
         * fist we need to check if params has content. if NO, doctrine requires us to use
         * inline filter-parameters. if YES, we have to add the filter-parameters to the
         * params array. wft?
         */
        $mode = count($params) > 0 ? 'params' : 'inline';

        $filterTokens = Bee_Utils_Strings::tokenizeToArray($restrictionHolder->getFilterString(), ' ');
        foreach ($filterTokens as $no => $token) {
            $andWhereStringParams = array();
            $andWhereString = null;

            foreach ($restrictionHolder->getFilterableFields() as $fieldName) {
                // $fieldName MUST BE A DOCTRINE NAME
                if (!is_null($andWhereString)) {
                    $andWhereString .= ' OR ';
                }

                if ($mode == 'params') {
                    $tokenName = ':filtertoken' . $no;
                    $andWhereString .= $fieldName . ' LIKE ' . $tokenName;
                    $params[$tokenName] = '%' . $token . '%';

                } else {
                    $andWhereString .= $fieldName . ' LIKE ?';
                    $andWhereStringParams[] = '%' . $token . '%';
                }
            }
            if ($mode == 'params') {
                $query->andWhere($andWhereString);

            } else {
                if (!is_null($andWhereString) && Bee_Utils_Strings::hasText($andWhereString)) {
                    $query->andWhere($andWhereString, $andWhereStringParams);
                }
            }
        }
    }

    /**
     * @param Doctrine_Query $query
     * @param array $params
     * @param Bee_Persistence_IOrderAndLimitHolder $orderAndLimitHolder
     */
    protected final function applyOrderAndLimit(Doctrine_Query &$query, array &$params, Bee_Persistence_IOrderAndLimitHolder $orderAndLimitHolder = null, array $defaultOrderMapping = array()) {
        if (is_null($orderAndLimitHolder)) {
            $orderMapping = $defaultOrderMapping;
        } else {
            $orderMapping = count($orderAndLimitHolder->getOrderMapping()) > 0 ? $orderAndLimitHolder->getOrderMapping() : $defaultOrderMapping;
        }

        foreach ($orderMapping as $orderField => $orderDir) {
//            $query->orderBy($orderField.' '.$orderDir);
            $query->addOrderBy($orderField . ' ' . $orderDir);
        }

        if (is_null($orderAndLimitHolder)) {
            return;
        }

        if ($orderAndLimitHolder->getPageSize() > 0) {
            $query->limit($orderAndLimitHolder->getPageSize());

            $pageCount = ceil($query->count($params) / $orderAndLimitHolder->getPageSize());
            $orderAndLimitHolder->setPageCount($pageCount);

            if ($orderAndLimitHolder->getCurrentPage() > $pageCount) {
                $orderAndLimitHolder->setCurrentPage($pageCount);
            }
            $query->offset($orderAndLimitHolder->getCurrentPage() * $orderAndLimitHolder->getPageSize());
        }
    }

}