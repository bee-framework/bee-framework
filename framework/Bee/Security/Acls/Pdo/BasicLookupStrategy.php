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
use Bee\Utils\Assert;

/**
 * User: mp
 * Date: Mar 16, 2010
 * Time: 11:38:06 PM
 */

class Bee_Security_Acls_Pdo_BasicLookupStrategy implements Bee_Security_Acls_Pdo_ILookupStrategy {

	public static function DEFAULT_SELECT_CLAUSE() {
		return 'SELECT acl_object_identity.object_id_identity, '
				. 'acl_entry.ace_order, '
				. 'acl_object_identity.id as acl_id, '
				. 'acl_object_identity.parent_object, '
				. 'acl_object_identity.entries_inheriting, '
				. 'acl_entry.id as ace_id, '
				. 'acl_entry.mask, '
				. 'acl_entry.granting, '
				. 'acl_entry.audit_success, '
				. 'acl_entry.audit_failure, '
				. 'acl_sid.principal as ace_principal, '
				. 'acl_sid.sid as ace_sid, '
				. 'acli_sid.principal as acl_principal, '
				. 'acli_sid.sid as acl_sid, '
				. 'acl_class.class '
				. 'FROM acl_object_identity '
				. 'LEFT JOIN acl_sid acli_sid ON acli_sid.id = acl_object_identity.owner_sid '
				. 'LEFT JOIN acl_class ON acl_class.id = acl_object_identity.object_id_class '
				. 'LEFT JOIN acl_entry ON acl_object_identity.id = acl_entry.acl_object_identity '
				. 'LEFT JOIN acl_sid ON acl_entry.sid = acl_sid.id '
				. 'WHERE ( ';
	}

	const DEFAULT_LOOKUP_KEYS_WHERE_CLAUSE = '(acl_object_identity.id = ?)';

	const DEFAULT_LOOKUP_IDENTITIES_WHERE_CLAUSE = '(acl_object_identity.object_id_identity = ? AND acl_class.class = ?)';

	const DEFAULT_ORDER_BY_CLAUSE = ') ORDER BY acl_object_identity.object_id_identity ASC, acl_entry.ace_order ASC';

	/**
	 * @var int
	 */
	private $batchSize = 50;

	/**
	 * @var Bee_Persistence_Pdo_Template
	 */
	private $pdoTemplate;

	/**
	 * @var Bee_Security_Acls_IAclAuthorizationStrategy
	 */
	private $aclAuthorizationStrategy;

	/**
	 * @var Bee_Security_Acls_IAuditLogger
	 */
	private $auditLogger;

	/**
	 * @var Bee_Security_Acls_IPermissionFactory
	 */
	private $permissionFactory;

	public function __construct(PDO $pdoConnection, Bee_Security_Acls_IAclAuthorizationStrategy $aclAuthorizationStrategy,
								Bee_Security_Acls_IAuditLogger $auditLogger,
								Bee_Security_Acls_IPermissionFactory $permissionFactory) {
		$this->pdoTemplate = new Bee_Persistence_Pdo_Template($pdoConnection);
		$this->aclAuthorizationStrategy = $aclAuthorizationStrategy;
		$this->auditLogger = $auditLogger;
		$this->permissionFactory = $permissionFactory;
	}

	/**
	 * Gets the BatchSize
	 *
	 * @return int $batchSize
	 */
	public function getBatchSize() {
		return $this->batchSize;
	}

	/**
	 * Sets the BatchSize
	 *
	 * @param $batchSize int
	 * @return void
	 */
	public function setBatchSize($batchSize) {
		$this->batchSize = $batchSize;
	}

	/**
	 * Gets the PdoTemplate
	 *
	 * @return Bee_Persistence_Pdo_Template $pdoTemplate
	 */
	public function getPdoTemplate() {
		return $this->pdoTemplate;
	}

	/**
	 * Sets the PdoTemplate
	 *
	 * @param $pdoTemplate Bee_Persistence_Pdo_Template
	 * @return void
	 */
	public function setPdoTemplate(Bee_Persistence_Pdo_Template $pdoTemplate) {
		$this->pdoTemplate = $pdoTemplate;
	}

	private static function computeRepeatingSql($repeatingSql, $requiredRepetitions) {
		Assert::isTrue($requiredRepetitions >= 1, 'Must be => 1');
		$sqlStringBuffer = array_fill(0, $requiredRepetitions, $repeatingSql);
		return self::DEFAULT_SELECT_CLAUSE() . join(' OR ', $sqlStringBuffer) . self::DEFAULT_ORDER_BY_CLAUSE;
	}

	/**
	 * @throws InvalidArgumentException
	 * @param Bee_Security_Acls_IObjectIdentity[] $objects
	 * @param Bee_Security_Acls_ISid[] $sids
	 * @return Bee_Security_Acls_IAcl[]
	 */
	public function readAclsByOidsAndSids($objects, $sids) {
		Assert::isTrue($this->batchSize >= 1, 'BatchSize must be >= 1');
		Assert::isTrue(count($objects), 'Objects to lookup required');

		// Map<ObjectIdentity,Acl>
		$result = array(); // contains FULLY loaded Acl objects

		$currentBatchToLoad = array(); // contains ObjectIdentitys

		$i = 0;
		foreach ($objects as $oid) {
			$i++;
			$aclFound = false;

			// Check we don't already have this ACL in the results
			if (array_key_exists($oid->getIdentifierString(), $result)) {
				$aclFound = true;
			}

			// Check cache for the present ACL entry
			// todo: devise a method to efficiently cache acls in PHP... tricky one...
//            if (!$aclFound) {
//            	$acl = $this->aclCache->getFromCache($oid);
//
//                // Ensure any cached element supports all the requested SIDs
//                // (they should always, as our base impl doesn't filter on SID)
//                if ($acl != null) {
//                    if ($acl->isSidLoaded($sids)) {
//                        $result[$acl->getObjectIdentity()->getIdentifierString()] = $acl;
//                        $aclFound = true;
//                    } else {
//                        throw new IllegalStateException(
//                            "Error: SID-filtered element detected when implementation does not perform SID filtering "
//                                    + "- have you added something to the cache manually?");
//                    }
//                }
//            }

			// Load the ACL from the database
			if (!$aclFound) {
				$currentBatchToLoad[$oid->getIdentifierString()] = $oid;
			}

			// Is it time to load from JDBC the currentBatchToLoad?
			$currBatchSize = count($currentBatchToLoad);
			if ($currBatchSize > 0 && (($currBatchSize == $this->batchSize) || ($i == count($objects)))) {
				$loadedBatch = $this->lookupObjectIdentities($currentBatchToLoad, $sids);

				// Add loaded batch (all elements 100% initialized) to results

				$result = array_merge($result, $loadedBatch);

				// Add the loaded batch to the cache
//                Iterator loadedAclIterator = loadedBatch.values().iterator();
//                while (loadedAclIterator.hasNext()) {
//                    aclCache.putInCache((AclImpl) loadedAclIterator.next());
//                }

				$currentBatchToLoad = array();
			}
		}

		return $result;
	}

	/**
	 * Looks up a batch of <code>ObjectIdentity</code>s directly from the database.<p>The caller is responsible
	 * for optimization issues, such as selecting the identities to lookup, ensuring the cache doesn't contain them
	 * already, and adding the returned elements to the cache etc.</p>
	 *  <p>This subclass is required to return fully valid <code>Acl</code>s, including properly-configured
	 * parent ACLs.</p>
	 *
	 * @param Bee_Security_Acls_IObjectIdentity[] $objectIdentities associative $oid->getIdentifierString() => $oid
	 * @param Bee_Security_Acls_ISid[] $sids DOCUMENT ME!
	 *
	 * @return Bee_Security_Acls_IAcl[] fully loaded associative $oid->getIdentifierString() => $acl
	 */
	private function lookupObjectIdentities($objectIdentities, $sids) {
		Assert::isTrue(count($objectIdentities) > 0, 'Must provide identities to lookup');

		$acls = array(); // contains Acls with StubAclParents

		// Make the "acls" map contain all requested objectIdentities
		// (including markers to each parent in the hierarchy)
		$sql = self::computeRepeatingSql(self::DEFAULT_LOOKUP_IDENTITIES_WHERE_CLAUSE, count($objectIdentities));

		$parentsToLookup = $this->pdoTemplate->queryBySqlString($sql,
			new Bee_Security_Acls_Pdo_BasicLookupStrategy_StatementSetter_lookupObjectIdentities($objectIdentities),
			new Bee_Security_Acls_Pdo_BasicLookupStrategy_ResultSetExtractor($acls, $sids, $this));

		// Lookup the parents, now that our JdbcTemplate has released the database connection (SEC-547)
		if (count($parentsToLookup) > 0) {
			$this->lookupPrimaryKeys($acls, $parentsToLookup, $sids);
		}

		// Finally, convert our "acls" containing StubAclParents into true Acls
		// todo: could also return the $acls array, couldn't we?
		$resultMap = array();

		foreach ($acls as $inputAcl) {
			Assert::isInstanceOf('Bee_Security_Acls_Impl_Acl', $inputAcl, 'Map should have contained an AclImpl');

			$result = $this->convert($acls, $inputAcl->getId());
			$resultMap[$result->getObjectIdentity()->getIdentifierString()] = $result;
		}

		return $resultMap;
	}

	/**
	 * Accepts the current <code>ResultSet</code> row, and converts it into an <code>AclImpl</code> that
	 * contains a <code>StubAclParent</code>
	 *
	 * @param Bee_Security_Acls_IAcl[] acls the Map we should add the converted Acl to
	 * @param rs the ResultSet focused on a current row
	 *
	 * @throws SQLException if something goes wrong converting values
	 * @throws IllegalStateException DOCUMENT ME!
	 */
	function convertCurrentResultIntoObject(&$acls, $row) {
		$id = $row['acl_id'];

		// If we already have an ACL for this ID, just create the ACE
		if (array_key_exists($id, $acls)) {
			$acl = $acls[$id];
		} else {
			// Make an AclImpl and pop it into the Map
			// todo: new instance of ObjectIdentity generated. can we avoid this?
			$objectIdentity = new Bee_Security_Acls_Impl_ObjectIdentity($row['class'], $row['object_id_identity']);

			$parentAcl = $row['parent_object'] ? new Bee_Security_Acls_Pdo_BasicLookupStrategy_StubAclParent($row['parent_object']) : null;

			$entriesInheriting = $row['entries_inheriting'];

			if ($row['acl_principal']) {
				$owner = new Bee_Security_Acls_Impl_PrincipalSid($row['acl_sid']);
			} else {
				$owner = new Bee_Security_Acls_Impl_GrantedAuthoritySid($row['acl_sid']);
			}

			$acl = new Bee_Security_Acls_Impl_Acl($objectIdentity, $id, $this->aclAuthorizationStrategy,
				$this->auditLogger, $parentAcl, null, $entriesInheriting, $owner);
			$acls[$id] = $acl;
		}

		// Add an extra ACE to the ACL (ORDER BY maintains the ACE list order)
		// It is permissable to have no ACEs in an ACL (which is detected by a null ACE_SID)
		if (!is_null($row['ace_sid'])) {
			$aceId = $row['ace_id'];

			if (!$acl->hasAce($aceId)) {
				if ($row['ace_principal']) {
					$recipient = new Bee_Security_Acls_Impl_PrincipalSid($row['ace_sid']);
				} else {
					$recipient = new Bee_Security_Acls_Impl_GrantedAuthoritySid($row['ace_sid']);
				}

				$mask = $row['mask'];
				$permission = $this->permissionFactory->buildFromMask($mask);
				$granting = $row['granting'];
				$auditSuccess = $row['audit_success'];
				$auditFailure = $row['audit_failure'];

				$ace = new Bee_Security_Acls_Impl_AccessControlEntry($aceId, $acl, $recipient, $permission, $granting,
					$auditSuccess, $auditFailure);

				$acl->addAce($ace);
			}
		}
	}

	/**
	 * @param Bee_Security_Acls_Impl_Acl[] $inputMap
	 * @param long $currentIdentity
	 * @return Bee_Security_Acls_Impl_Acl
	 */
	private function convert(&$inputMap, $currentIdentity) {

		// Retrieve this Acl from the InputMap
		$inputAcl = $inputMap[''.$currentIdentity];

		$parent = $inputAcl->getParentAcl();

		if (!is_null($parent) && $parent instanceof Bee_Security_Acls_Pdo_BasicLookupStrategy_StubAclParent) {
			// Lookup the parent
			$parent = $this->convert($inputMap, $parent->getId());

//			$inputAcl->setParentAcl($parent);
		}

//		return $inputAcl;

		// Now we have the parent (if there is one), create the true AclImpl
        $result = new Bee_Security_Acls_Impl_Acl($inputAcl->getObjectIdentity(), $inputAcl->getId(),
            $this->aclAuthorizationStrategy, $this->auditLogger, $parent, null, $inputAcl->isEntriesInheriting(),
            $inputAcl->getOwner());

		// Iterate over the "aces" input and replace each nested AccessControlEntryImpl.getAcl() with the new "result" AclImpl instance
		// This ensures StubAclParent instances are removed, as per SEC-951
        foreach($inputAcl->getEntries() as $ace) {
            $ace->setAcl($result);
            $result->addAce($ace);
        }

        return $result;
	}

	/**
	 * Locates the primary key IDs specified in "findNow", adding AclImpl instances with StubAclParents to the
	 * "acls" Map.
	 *
	 * @param acls the AclImpls (with StubAclParents)
	 * @param findNow Long-based primary keys to retrieve
	 * @param sids DOCUMENT ME!
	 */
	private function lookupPrimaryKeys(array &$acls, array $findNow, array $sids = null) {

		$sql = self::computeRepeatingSql(self::DEFAULT_LOOKUP_KEYS_WHERE_CLAUSE, count($findNow));

		$parentsToLookup = $this->pdoTemplate->queryBySqlString($sql,
			new Bee_Security_Acls_Pdo_BasicLookupStrategy_StatementSetter_lookupPrimaryKeys($findNow),
			new Bee_Security_Acls_Pdo_BasicLookupStrategy_ResultSetExtractor($acls, $sids, $this));

		if (count($parentsToLookup) > 0) {
			$this->lookupPrimaryKeys($acls, $parentsToLookup, $sids);
		}
	}
}

class Bee_Security_Acls_Pdo_BasicLookupStrategy_StatementSetter_lookupObjectIdentities
	implements Bee_Persistence_Pdo_IStatementSetter {

	/**
	 * @var Bee_Security_Acls_IObjectIdentity[]
	 */
	private $objectIdentities;

	public function __construct(&$objectIdentities) {
		$this->objectIdentities = & $objectIdentities;
	}

	public function setValues(PDOStatement $ps) {
		$i = 0;
		foreach ($this->objectIdentities as $objectIdentity) {
			// Inject values
			$ps->bindValue((2 * $i) + 1, $objectIdentity->getIdentifier(), PDO::PARAM_INT);
			$ps->bindValue((2 * $i) + 2, $objectIdentity->getType(), PDO::PARAM_STR);
			$i++;
		}
		$ps->setFetchMode(PDO::FETCH_ASSOC);
	}
}

class Bee_Security_Acls_Pdo_BasicLookupStrategy_StatementSetter_lookupPrimaryKeys
	implements Bee_Persistence_Pdo_IStatementSetter {

	/**
	 * @var array
	 */
	private $findNow;

	public function __construct($findNow) {
		$this->findNow = $findNow;
	}

	public function setValues(PDOStatement $ps) {
		$i = 0;
		foreach ($this->findNow as $id => $dummy) {
			$i++;
			$ps->bindValue($i, $id, PDO::PARAM_INT);
		}
		$ps->setFetchMode(PDO::FETCH_ASSOC);
	}
}

class Bee_Security_Acls_Pdo_BasicLookupStrategy_ResultSetExtractor implements Bee_Persistence_Pdo_IResultSetExtractor {

	/**
	 * @var Bee_Security_Acls_IAcl[]
	 */
	private $acls;

	/**
	 * @var Bee_Security_Acls_ISid[]
	 */
	private $sids;

	/**
	 * @var Bee_Security_Acls_Pdo_BasicLookupStrategy
	 */
	private $strategy;

	public function __construct(&$acls, $sids, Bee_Security_Acls_Pdo_BasicLookupStrategy $strategy) {
		$this->acls = & $acls;
		$this->sids = $sids;
		$this->strategy = $strategy;
	}

	public function extractData(PDOStatement $rs) {
		$parentIdsToLookup = array(); // Set of parent_id Longs

		foreach ($rs as $row) {
			// Convert current row into an Acl (albeit with a StubAclParent)
			$this->strategy->convertCurrentResultIntoObject($this->acls, $row);

			// Figure out if this row means we need to lookup another parent
			$parentId = $row['parent_object'];

			if ($parentId != 0) {
				// See if it's already in the "acls"
				if (array_key_exists($parentId, $this->acls)) {
					continue; // skip this while iteration
				}

				// Now try to find it in the cache
//                $cached = $aclCache.getFromCache(new Long(parentId));

//                if ((cached == null) || !cached.isSidLoaded(sids)) {
				$parentIdsToLookup[$parentId] = true;
//                } else {
				// Pop into the acls map, so our convert method doesn't
				// need to deal with an unsynchronized AclCache
//                    acls.put(cached.getId(), cached);
//                }
			}
		}

		// Return the parents left to lookup to the calller
		return $parentIdsToLookup;
	}
}

class Bee_Security_Acls_Pdo_BasicLookupStrategy_StubAclParent implements Bee_Security_Acls_IAcl{

	/**
	 * @var int
	 */
	private $id;

	/**
	 * @param $id int
	 */
	function __construct($id) {
		$this->id = $id;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	public function getEntries() {
		throw new Exception('Unsupported operation, stub only');
	}

	public function getObjectIdentity() {
		throw new Exception('Unsupported operation, stub only');
	}

	public function getOwner() {
		throw new Exception('Unsupported operation, stub only');
	}

	public function getParentAcl() {
		throw new Exception('Unsupported operation, stub only');
	}

	public function isEntriesInheriting() {
		throw new Exception('Unsupported operation, stub only');
	}

	public function isGranted(array $permission, array $sids, $administrativeMode) {
		throw new Exception('Unsupported operation, stub only');
	}

	public function isSidLoaded(array $sids) {
		throw new Exception('Unsupported operation, stub only');
	}
}
