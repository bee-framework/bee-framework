<?php
namespace Bee\Security\Vote;
/*
 * Copyright 2008-2015 the original author or authors.
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
use Bee\Security\Acls\Exception\NotFoundException;
use Bee\Security\Acls\IAclService;
use Bee\Security\Acls\Impl\ObjectIdentityRetrievalStrategy;
use Bee\Security\Acls\Impl\SidRetrievalStrategy;
use Bee\Security\Acls\IObjectIdentityRetrievalStrategy;
use Bee\Security\Acls\IPermission;
use Bee\Security\Acls\ISidRetrievalStrategy;
use Bee\Security\ConfigAttributeDefinition;
use Bee\Security\Exception\GenericSecurityException;
use Bee\Security\IAuthentication;
use Bee\Security\IConfigAttribute;
use Bee\Utils\Strings;
use Bee\Utils\TLogged;

/**
 * Created by IntelliJ IDEA.
 * User: mp
 * Date: Mar 22, 2010
 * Time: 11:25:08 PM
 * To change this template use File | Settings | File Templates.
 */

class AclEntryVoter extends AbstractAclVoter {
    use TLogged;

    /**
     * @var IAclService
     */
    private $aclService;

    /**
     * @var IObjectIdentityRetrievalStrategy
     */
    private $objectIdentityRetrievalStrategy;

    /**
     * @var ISidRetrievalStrategy
     */
    private $sidRetrievalStrategy;

    /**
     * @var string
     */
    private $internalMethod;

    /**
     * @var string
     */
    private $processConfigAttribute;

    /**
     * @var IPermission[]
     */
    private $requirePermission;

    /**
     * @param IAclService $aclService
     * @param $processConfigAttribute
     * @param array $requirePermission
     */
    public function __construct(IAclService $aclService,
                                $processConfigAttribute,
                                array $requirePermission) {
        $this->aclService = $aclService;
        $this->objectIdentityRetrievalStrategy = new ObjectIdentityRetrievalStrategy();
        $this->sidRetrievalStrategy = new SidRetrievalStrategy();
        $this->processConfigAttribute = $processConfigAttribute;
        $this->requirePermission = $requirePermission;
    }

    /**
     * Gets the InternalMethod
     *
     * @return string $internalMethod
     */
    public function getInternalMethod() {
        return $this->internalMethod;
    }

    /**
     * Sets the InternalMethod
     *
     * @param $internalMethod string
     * @return void
     */
    public function setInternalMethod($internalMethod) {
        $this->internalMethod = $internalMethod;
    }

    /**
     * @return string
     */
    public function getProcessConfigAttribute() {
        return $this->processConfigAttribute;
    }

    /**
     * @param IObjectIdentityRetrievalStrategy $objectIdentityRetrievalStrategy
     * @return void
     */
    public function setObjectIdentityRetrievalStrategy(IObjectIdentityRetrievalStrategy $objectIdentityRetrievalStrategy) {
        $this->objectIdentityRetrievalStrategy = $objectIdentityRetrievalStrategy;
    }

    /**
     * @param ISidRetrievalStrategy $sidIdentityRetrievalStrategy
     * @return void
     */
    public function setSidRetrievalStrategy(ISidRetrievalStrategy $sidIdentityRetrievalStrategy) {
        $this->sidRetrievalStrategy = $sidIdentityRetrievalStrategy;
    }

    /**
     * @param IConfigAttribute $configAttribute
     * @return bool
     */
    public function supports(IConfigAttribute $configAttribute) {
        return $configAttribute->getAttribute() == $this->getProcessConfigAttribute();
    }

    /**
     * @param IAuthentication $authentication
     * @param mixed $object
     * @param ConfigAttributeDefinition $config
     * @return int
     * @throws GenericSecurityException
     */
    public function vote(IAuthentication $authentication, $object, ConfigAttributeDefinition $config) {
        foreach($config->getConfigAttributes() as $attr) {

            if (!$this->supports($attr)) {
                continue;
            }

            // Need to make an access decision on this invocation
            // Attempt to locate the domain object instance to process
            $domainObject = $this->getDomainObjectInstance($object);

            // If domain object is null, vote to abstain
            if ($domainObject == null) {
                if ($this->getLog()->isDebugEnabled()) {
                    $this->getLog()->debug('Voting to abstain - domainObject is null');
                }

                return self::ACCESS_ABSTAIN;
            }

            // Evaluate if we are required to use an inner domain object
            if (Strings::hasText($this->internalMethod)) {
                if(!method_exists($domainObject, $this->internalMethod)) {
                    throw new GenericSecurityException('Object of class ' .
                            get_class($domainObject) . ' does not provide requested method ' .
                            $this->internalMethod);
                }

                $domainObject = call_user_method($this->internalMethod, $domainObject);
            }

            // Obtain the OID applicable to the domain object
            $objectIdentity = $this->objectIdentityRetrievalStrategy->getObjectIdentity($domainObject);

            // Obtain the SIDs applicable to the principal
            $sids = $this->sidRetrievalStrategy->getSids($authentication);

            $acl = null;

            try {
                // Lookup only ACLs for SIDs we're interested in
                $acl = $this->aclService->readAclForOidAndSids($objectIdentity, $sids);
            } catch (NotFoundException $nfe) {
                if ($this->getLog()->isDebugEnabled()) {
                    $this->getLog()->debug('Voting to deny access - no ACLs apply for this principal');
                }

                return self::ACCESS_DENIED;
            }

            try {
                if ($acl->isGranted($this->requirePermission, $sids, false)) {
                    if ($this->getLog()->isDebugEnabled()) {
                        $this->getLog()->debug('Voting to grant access');
                    }

                    return self::ACCESS_GRANTED;
                } else {
                    if ($this->getLog()->isDebugEnabled()) {
                        $this->getLog()->debug('Voting to deny access - ACLs returned, but insufficient permissions for this principal');
                    }

                    return self::ACCESS_DENIED;
                }
            } catch (NotFoundException $nfe) {
                if ($this->getLog()->isDebugEnabled()) {
                    $this->getLog()->debug('Voting to deny access - no ACLs apply for this principal');
                }

                return self::ACCESS_DENIED;
            }
        }

        // No configuration attribute matched, so abstain
        return self::ACCESS_ABSTAIN;
    }
}