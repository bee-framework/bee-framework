<?php
namespace Bee\Persistence\Pdo\Behaviors;

use Bee\Persistence\Behaviors\NestedSet\IDelegate;
use Bee\Persistence\Behaviors\NestedSet\NodeInfo;
use Bee\Persistence\Pdo\FeatureDetector;
use Bee\Persistence\Pdo\Utils;

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

/**
 * User: mp
 * Date: 07.05.13
 * Time: 18:06
 */

class GenericNestedSetDelegate extends DelegateBase implements IDelegate {

	const GET_POSITION_QUERY_TEMPLATE = 'SELECT %1$s AS lft, %2$s AS rgt, %3$s AS lvl FROM %4$s WHERE %5$s AND %6$s';
	const GET_TREE_INFO_QUERY_TEMPLATE = 'SELECT MIN(%1$s) AS lft, MAX(%2$s) AS rgt, 0 AS lvl FROM %3$s WHERE %4$s';
	const SET_POSITION_QUERY_TEMPLATE = 'UPDATE %4$s SET %1$s = %1$s + :pos_delta, %2$s = %2$s + :pos_delta, %3$s = %3$s + :lvl_delta WHERE %1$s >= :lft AND %2$s <= :rgt AND %5$s';
	const SET_POSITION_QUERY_BY_ID_TEMPLATE = 'UPDATE %4$s SET %1$s = :lft, %2$s = :rgt, %3$s = :lvl WHERE %5$s AND %6$s';
	const SHIFT_QUERY_TEMPLATE = 'UPDATE %1$s SET %2$s = %2$s + :delta WHERE %2$s >= :lower_bound AND %2$s < :upper_bound AND %3$s';
	const SHIFT_QUERY_OPEN_TEMPLATE = 'UPDATE %1$s SET %2$s = %2$s + :delta WHERE %2$s >= :lower_bound AND %3$s';

	/**
	 * @var string
	 */
	private $leftFieldName = 'lft';

	/**
	 * @var string
	 */
	private $rightFieldName = 'rgt';

	/**
	 * @var string
	 */
	private $levelFieldName = 'lvl';

	/**
	 * @param string $leftFieldName
	 */
	public function setLeftFieldName($leftFieldName) {
		$this->leftFieldName = $leftFieldName;
	}

	/**
	 * @param string $levelFieldName
	 */
	public function setLevelFieldName($levelFieldName) {
		$this->levelFieldName = $levelFieldName;
	}

	/**
	 * @param string $rightFieldName
	 */
	public function setRightFieldName($rightFieldName) {
		$this->rightFieldName = $rightFieldName;
	}


	/**
	 * @param mixed $nestedSetEntity
	 * @param mixed $restriction
	 * @return NodeInfo
	 */
	public function getNodeInfo($nestedSetEntity, $restriction = false) {
		$params = array();
		$qryString = sprintf(self::GET_POSITION_QUERY_TEMPLATE, $this->leftFieldName, $this->rightFieldName,
			$this->levelFieldName, $this->getQueryDomain(), $this->getIdentityRestrictionString($nestedSetEntity, $params),
			$this->getDomainRestrictionString($nestedSetEntity, $params, $restriction));
		return new NodeInfo(Utils::fetchRow($this->getPdo()->prepare($qryString), $params));
	}

	/**
	 * @param mixed $nestedSetEntity
	 * @param mixed $restriction
	 * @return NodeInfo
	 */
	public function getTreeInfo($nestedSetEntity, $restriction = false) {
		$params = array();
		$qryString = sprintf(self::GET_TREE_INFO_QUERY_TEMPLATE, $this->leftFieldName, $this->rightFieldName,
			$this->getQueryDomain(), $this->getDomainRestrictionString($nestedSetEntity, $params, $restriction));
		return new NodeInfo(Utils::fetchRow($this->getPdo()->prepare($qryString), $params));
	}

	/**
	 * @param mixed $nestedSetEntity
	 * @param NodeInfo $oldInfo
	 * @param int $newLft
	 * @param int $newLvl
	 * @param mixed $restriction
	 */
	public function setPosition($nestedSetEntity, NodeInfo $oldInfo, $newLft, $newLvl, $restriction = false) {
		if ($oldInfo->hasStructure()) {
			$params = array(':pos_delta' => $newLft - $oldInfo->lft, ':lvl_delta' => $newLvl - $oldInfo->lvl, ':lft' => $oldInfo->lft, ':rgt' => $oldInfo->rgt);
			$qryString = sprintf(self::SET_POSITION_QUERY_TEMPLATE, $this->leftFieldName, $this->rightFieldName, $this->levelFieldName,
				$this->getQueryDomain(), $this->getDomainRestrictionString($nestedSetEntity, $params, $restriction));
		} else {
			$params = array(':lft' => $newLft, ':rgt' => $newLft + $oldInfo->getSpan() - 1, ':lvl' => $newLvl);
			$qryString = sprintf(self::SET_POSITION_QUERY_BY_ID_TEMPLATE, $this->leftFieldName, $this->rightFieldName,
				$this->levelFieldName, $this->getQueryDomain(),
				$this->getIdentityRestrictionString($nestedSetEntity, $params),
				$this->getDomainRestrictionString($nestedSetEntity, $params, $restriction));
		}
		$this->getPdo()->prepare($qryString)->execute($params);
	}

	/**
	 * @param mixed $nestedSetEntity
	 * @param int $delta
	 * @param int $lowerBoundIncl
	 * @param int $upperBoundExcl
	 * @param mixed $restriction
	 */
	public function shift($nestedSetEntity, $delta, $lowerBoundIncl, $upperBoundExcl, $restriction = false) {
		$params = array(':delta' => $delta, ':lower_bound' => $lowerBoundIncl);
		if ($upperBoundExcl !== false) {
			$params[':upper_bound'] = $upperBoundExcl;
		}
		$qryTempl = $upperBoundExcl !== false ? self::SHIFT_QUERY_TEMPLATE : self::SHIFT_QUERY_OPEN_TEMPLATE;

		$qryDomain = $this->getQueryDomain();
		$domRes = $this->getDomainRestrictionString($nestedSetEntity, $params, $restriction);

		// order updates only if supported by the driver and not operating on a joined relation
		$orderUpdate = $this->pdoSupportsFeature(FeatureDetector::FEATURE_ORDERED_UPDATE) && stripos($qryDomain, ' JOIN ') === false;

		// update left positions
		$qryString = sprintf($qryTempl, $qryDomain, $this->leftFieldName, $domRes);
//		if ($upperBoundExcl !== false && $orderUpdate) {
		if ($orderUpdate) {
			$qryString .= ' ORDER BY ' . $this->leftFieldName . ($delta > 0 ? ' DESC' : ' ASC');
		}
		$this->getPdo()->prepare($qryString)->execute($params);

		// update right positions
		$qryString = sprintf($qryTempl, $qryDomain, $this->rightFieldName, $domRes);
//		if ($upperBoundExcl !== false && $orderUpdate) {
		if ($orderUpdate) {
			$qryString .= ' ORDER BY ' . $this->rightFieldName . ($delta > 0 ? ' DESC' : ' ASC');
		}
		$this->getPdo()->prepare($qryString)->execute($params);
	}
}
