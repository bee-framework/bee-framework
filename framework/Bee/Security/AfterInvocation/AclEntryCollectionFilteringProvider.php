<?php
namespace Bee\Security\AfterInvocation;
/*
 * Copyright 2008-2014 the original author or authors.
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
use ArrayAccess;
use Bee\Framework;
use Bee\Security\Acls\IAclService;
use Bee\Security\Acls\IObjectIdentity;
use Bee\Security\ConfigAttributeDefinition;
use Bee\Security\Exception\GenericSecurityException;
use Bee\Security\IAuthentication;
use Logger;
use Traversable;

/**
 * Class AclEntryCollectionFilteringProvider
 * @package Bee\Security\AfterInvocation
 */
class AclEntryCollectionFilteringProvider extends AbstractAclProvider {

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

    public function __construct(IAclService $aclService,
                                $processConfigAttribute,
                                array $requirePermission) {
        parent::__construct($aclService, $processConfigAttribute, $requirePermission);
    }

    public function decide(IAuthentication $authentication, $object, ConfigAttributeDefinition $config,
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
                throw new GenericSecurityException('returned object must be Traversable');
            }

			/** @var IObjectIdentity[] $identities */
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