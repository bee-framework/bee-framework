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

/**
 * Created by IntelliJ IDEA.
 * User: mp
 * Date: Mar 22, 2010
 * Time: 11:25:08 PM
 * To change this template use File | Settings | File Templates.
 */

class Bee_Security_Vote_AclEntryVoter extends Bee_Security_Vote_AbstractAclVoter {

    /**
     * @var Bee_Security_Acls_IAclService
     */
    private $aclService;

    /**
     * @var Bee_Security_Acls_IObjectIdentityRetrievalStrategy
     */
    private $objectIdentityRetrievalStrategy;

    /**
     * @var Bee_Security_Acls_ISidRetrievalStrategy
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
     * @var Bee_Security_Acls_IPermission[]
     */
    private $requirePermission;

    public function __construct(Bee_Security_Acls_IAclService $aclService,
                                $processConfigAttribute,
                                array $requirePermission) {
        $this->aclService = $aclService;
        $this->objectIdentityRetrievalStrategy = new Bee_Security_Acls_Impl_ObjectIdentityRetrievalStrategy();
        $this->sidRetrievalStrategy = new Bee_Security_Acls_Impl_SidRetrievalStrategy();
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
     * @param Bee_Security_Acls_IObjectIdentityRetrievalStrategy $objectIdentityRetrievalStrategy
     * @return void
     */
    public function setObjectIdentityRetrievalStrategy(Bee_Security_Acls_IObjectIdentityRetrievalStrategy $objectIdentityRetrievalStrategy) {
        $this->objectIdentityRetrievalStrategy = $objectIdentityRetrievalStrategy;
    }

    /**
     * @param Bee_Security_Acls_ISidRetrievalStrategy $sidIdentityRetrievalStrategy
     * @return void
     */
    public function setSidRetrievalStrategy(Bee_Security_Acls_ISidRetrievalStrategy $sidIdentityRetrievalStrategy) {
        $this->sidRetrievalStrategy = $sidIdentityRetrievalStrategy;
    }

    public function supports(Bee_Security_IConfigAttribute $configAttribute) {
        return $configAttribute->getAttribute() == $this->getProcessConfigAttribute();
    }

    public function vote(Bee_Security_IAuthentication $authentication, $object,
                         Bee_Security_ConfigAttributeDefinition $config) {


        foreach($config->getConfigAttributes() as $attr) {

            if (!$this->supports($attr)) {
                continue;
            }

            // Need to make an access decision on this invocation
            // Attempt to locate the domain object instance to process
            $domainObject = $this->getDomainObjectInstance($object);

            // If domain object is null, vote to abstain
            if ($domainObject == null) {
                if (Bee_Utils_Logger::isDebugEnabled()) {
                    Bee_Utils_Logger::debug('Voting to abstain - domainObject is null');
                }

                return self::ACCESS_ABSTAIN;
            }

            // Evaluate if we are required to use an inner domain object
            if (Bee_Utils_Strings::hasText($this->internalMethod)) {
                if(!method_exists($domainObject, $this->internalMethod)) {
                    throw new Bee_Security_Exception_Generic('Object of class ' .
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
            } catch (Bee_Security_Acls_Exception_NotFound $nfe) {
                if (Bee_Utils_Logger::isDebugEnabled()) {
                    Bee_Utils_Logger::debug('Voting to deny access - no ACLs apply for this principal');
                }

                return self::ACCESS_DENIED;
            }

            try {
                if ($acl->isGranted($this->requirePermission, $sids, false)) {
                    if (Bee_Utils_Logger::isDebugEnabled()) {
                        Bee_Utils_Logger::debug('Voting to grant access');
                    }

                    return self::ACCESS_GRANTED;
                } else {
                    if (Bee_Utils_Logger::isDebugEnabled()) {
                        Bee_Utils_Logger::debug('Voting to deny access - ACLs returned, but insufficient permissions for this principal');
                    }

                    return self::ACCESS_DENIED;
                }
            } catch (Bee_Security_Acls_Exception_NotFound $nfe) {
                if (Bee_Utils_Logger::isDebugEnabled()) {
                    Bee_Utils_Logger::debug('Voting to deny access - no ACLs apply for this principal');
                }

                return self::ACCESS_DENIED;
            }
        }

        // No configuration attribute matched, so abstain
        return self::ACCESS_ABSTAIN;

    }
}
?>