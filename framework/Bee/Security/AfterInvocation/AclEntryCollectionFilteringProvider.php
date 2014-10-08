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
use Bee\Framework;

/**
 * User: mp
 * Date: Mar 23, 2010
 * Time: 2:20:25 PM
 */

class Bee_Security_AfterInvocation_AclEntryCollectionFilteringProvider extends Bee_Security_AfterInvocation_AbstractAclProvider {

	/**
	 * @var Logger
	 */
	protected static $log;

	/**
	 * @return Logger
	 */
	protected static function getLog() {
		if (!self::$log) {
			self::$log = Framework::getLoggerForClass(__CLASS__);
		}
		return self::$log;
	}

    public function __construct(Bee_Security_Acls_IAclService $aclService,
                                $processConfigAttribute,
                                array $requirePermission) {
        parent::__construct($aclService, $processConfigAttribute, $requirePermission);
    }

    public function decide(Bee_Security_IAuthentication $authentication, $object, Bee_Security_ConfigAttributeDefinition $config,
        $returnedObject) {

        if (is_null($returnedObject)) {
            if (self::getLog()->isDebugEnabled()) {
				self::getLog()->debug('Return object is null, skipping');
            }

            return null;
        }

        foreach($config->getConfigAttributes() as $attr) {

            if (!$this->supports($attr)) {
                continue;
            }

            if(!(is_array($returnedObject) || $returnedObject instanceof Traversable)) {
                throw new Bee_Security_Exception_Generic('returned object must be Traversable');
            }

            $identities = array();
            foreach($returnedObject as $domainObject) {
                $identities[] = $this->objectIdentityRetrievalStrategy->getObjectIdentity($this->getDomainObject($domainObject));
            }

            $sids = $this->sidRetrievalStrategy->getSids($authentication);

            $acls = $this->aclService->readAclsForOidsAndSids($identities, $sids, false);

            $i = 0;

            if(is_array($returnedObject) || $returnedObject instanceof ArrayAccess) {
                $resultObject = null;
            } else {
                $resultObject = array();
            }

            foreach($returnedObject as $idx => $domainObject) {
                $identity = $identities[$i++];

//                // Ignore nulls or entries which aren't instances of the configured domain object class
//                if (is_null($domainObject) || !Types::isAssignable($domainObject, $this->getProcessDomainObjectClass())) {
//                    continue;
//                }
                $acl = $acls[$identity->getIdentifierString()];

                if(!$this->hasAclPermission($acl, $sids)) {
                    if(!is_array($resultObject)) {
                        unset($returnedObject[$idx]);
                    }
                } else if(is_array($resultObject)) {
                    $resultObject[] = $domainObject;
                }
            }

            return is_array($resultObject) ? $resultObject : $returnedObject;
        }

        return $returnedObject;
    }

}
?>
