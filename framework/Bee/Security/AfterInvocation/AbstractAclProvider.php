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
use Bee\Security\Acls\Exception\NotFoundException;
use Bee\Security\Acls\IAcl;
use Bee\Security\Acls\IAclService;
use Bee\Security\Acls\Impl\ObjectIdentityRetrievalStrategy;
use Bee\Security\Acls\Impl\SidRetrievalStrategy;
use Bee\Security\Acls\IObjectIdentityRetrievalStrategy;
use Bee\Security\Acls\IPermission;
use Bee\Security\Acls\ISidRetrievalStrategy;
use Bee\Security\ConfigAttribute;
use Bee\Security\Exception\GenericSecurityException;
use Bee\Utils\Assert;
use Bee\Utils\ITypeDefinitions;
use Bee\Utils\Strings;

/**
 * Class AbstractAclProvider
 * @package Bee\Security\AfterInvocation
 */
abstract class AbstractAclProvider implements IProvider {

    /**
     * @var IAclService
     */
    protected $aclService;

    /**
     * @var string
     */
    protected $processDomainObjectClass = ITypeDefinitions::OBJECT_TYPE;

    /**
     * @var IObjectIdentityRetrievalStrategy
     */
    protected $objectIdentityRetrievalStrategy;

    /**
     * @var ISidRetrievalStrategy
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
     * @var IPermission[]
     */
    private $requirePermission;

    public function __construct(IAclService $aclService,
                                $processConfigAttribute,
                                array $requirePermission) {
        $this->aclService = $aclService;
        $this->objectIdentityRetrievalStrategy = new ObjectIdentityRetrievalStrategy();
        $this->sidRetrievalStrategy = new SidRetrievalStrategy();
        $this->processConfigAttribute = $processConfigAttribute;
        $this->requirePermission = $requirePermission;
    }

    protected function hasAclPermission(IAcl $acl = null, array $sids) {
        if(!is_null($acl)) {
            try {
                return $acl->isGranted($this->requirePermission, $sids, false);
            } catch (NotFoundException $nfe) {
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
		Assert::hasText($processDomainObjectClass, 'processDomainObjectClass cannot be empty');
        $this->processDomainObjectClass = $processDomainObjectClass;
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
        if (Strings::hasText($this->internalMethod)) {
            if(!method_exists($domainObject, $this->internalMethod)) {
                throw new GenericSecurityException('Object of class ' .
                        get_class($domainObject) . ' does not provide requested method ' .
                        $this->internalMethod);
            }

            $domainObject = call_user_method($this->internalMethod, $domainObject);
        }
        return $domainObject;
    }

    /**
     * @param ISidRetrievalStrategy $sidIdentityRetrievalStrategy
     * @return void
     */
    public function setSidRetrievalStrategy(ISidRetrievalStrategy $sidIdentityRetrievalStrategy) {
        $this->sidRetrievalStrategy = $sidIdentityRetrievalStrategy;
    }

	/**
	 * @param ConfigAttribute $attribute
	 * @return bool|true
	 */
    public function supports(ConfigAttribute $attribute) {
        return $attribute->getAttribute() == $this->getProcessConfigAttribute();
    }

	/**
	 * @param string $className
	 * @return bool|true
	 */
    public function supportsClass($className) {
        return true;
    }
}
