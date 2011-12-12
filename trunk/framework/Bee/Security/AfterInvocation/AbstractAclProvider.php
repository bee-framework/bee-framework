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
 * User: mp
 * Date: Mar 23, 2010
 * Time: 2:11:43 PM
 */

abstract class Bee_Security_AfterInvocation_AbstractAclProvider implements Bee_Security_AfterInvocation_IProvider {

    /**
     * @var Bee_Security_Acls_IAclService
     */
    protected $aclService;

    /**
     * @var string
     */
    protected $processDomainObjectClass = Bee_Utils_ITypeDefinitions::OBJECT_TYPE;

    /**
     * @var Bee_Security_Acls_IObjectIdentityRetrievalStrategy
     */
    protected $objectIdentityRetrievalStrategy;

    /**
     * @var Bee_Security_Acls_ISidRetrievalStrategy
     */
    protected $sidRetrievalStrategy;

    /**
     * @var string
     */
    protected $internalMethod;

    /**
     * @var string
     */
    protected $processConfigAttribute;

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

    protected function hasAclPermission(Bee_Security_Acls_IAcl $acl = null, array $sids) {
        if(!is_null($acl)) {
            try {
                return $acl->isGranted($this->requirePermission, $sids, false);
            } catch (Bee_Security_Acls_Exception_NotFound $nfe) {
                // fall through
            }
        }
        return false;
    }

    public function getProcessDomainObjectClass() {
        return $this->processDomainObjectClass;
    }

    /**
     * @param string $processDomainObjectClass
     * @return void
     */
    public function setProcessDomainObjectClass($processDomainObjectClass) {
        Bee_Utils_Assert::hasText($processDomainObjectClass, 'processDomainObjectClass cannot be empty');
        $this->processDomainObjectClass = $processDomainObjectClass;
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

    protected function getDomainObject($domainObject) {
        // Evaluate if we are required to use an inner domain object
        if (Bee_Utils_Strings::hasText($this->internalMethod)) {
            if(!method_exists($domainObject, $this->internalMethod)) {
                throw new Bee_Security_Exception_Generic('Object of class ' .
                        get_class($domainObject) . ' does not provide requested method ' .
                        $this->internalMethod);
            }

            $domainObject = call_user_method($this->internalMethod, $domainObject);
        }
        return $domainObject;
    }

    /**
     * @param Bee_Security_Acls_ISidRetrievalStrategy $sidIdentityRetrievalStrategy
     * @return void
     */
    public function setSidRetrievalStrategy(Bee_Security_Acls_ISidRetrievalStrategy $sidIdentityRetrievalStrategy) {
        $this->sidRetrievalStrategy = $sidIdentityRetrievalStrategy;
    }

    public function supports(Bee_Security_ConfigAttribute $attribute) {
        return $attribute->getAttribute() == $this->getProcessConfigAttribute();
    }

    public function supportsClass($className) {
        return true;
    }
}
?>
