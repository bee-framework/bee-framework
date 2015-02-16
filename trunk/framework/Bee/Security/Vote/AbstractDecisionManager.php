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
use Bee\Context\Config\IInitializingBean;
use Bee\Security\Exception\AccessDeniedException;
use Bee\Security\IAccessDecisionManager;
use Bee\Security\IConfigAttribute;
use Bee\Utils\Assert;

/**
 * Class AbstractDecisionManager
 * @package Bee\Security\Vote
 */
abstract class AbstractDecisionManager implements IAccessDecisionManager, IInitializingBean {

	/**
	 * 
	 * @var IAccessDecisionVoter[]
	 */
	private $decisionVoters;

	/**
	 * 
	 * @var boolean
	 */
	private $allowIfAllAbstainDecisions;

	/**
	 *
	 */
	public function afterPropertiesSet() {
		Assert::isTrue(count($this->decisionVoters) > 0, 'A list of AccessDecisionVoters is required');
	}

	/**
	 * @throws AccessDeniedException
	 */
	protected final function checkAllowIfAllAbstainDecisions() {
        if (!$this->isAllowIfAllAbstainDecisions()) {
            throw new AccessDeniedException('access_denied'); // @todo: message source
        }
    }

    /**
     * 
     * @return boolean
     */
	public function isAllowIfAllAbstainDecisions() {
		return $this->allowIfAllAbstainDecisions;
	}

	/**
	 * 
	 * @param boolean $allowIfAllAbstainDecisions
	 * @return void
	 */
    public function setAllowIfAllAbstainDecisions($allowIfAllAbstainDecisions) {
        $this->allowIfAllAbstainDecisions = $allowIfAllAbstainDecisions;
    }

	/**
	 * @return IAccessDecisionVoter[]
	 */
    public function getDecisionVoters() {
    	return $this->decisionVoters;
    }

	/**
	 * @param IAccessDecisionVoter[]|array $decisionVoters
	 */
    public function setDecisionVoters(array $decisionVoters) {
    	foreach($decisionVoters as $voter) {
			Assert::isInstanceOf('Bee\Security\Vote\IAccessDecisionVoter', $voter, 'AccessDecisionVoter ' . get_class($voter) . ' must implement Bee\Security\Vote\IAccessDecisionVoter');
    	}
    	$this->decisionVoters = $decisionVoters; 
    }

	/**
	 * @param IConfigAttribute $configAttribute
	 * @return bool
	 */
	public function supports(IConfigAttribute $configAttribute) {
		foreach($this->decisionVoters as $voter) {
			if($voter->supports($configAttribute)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @param string $className
	 * @return bool
	 */
	public function supportsClass($className) {
		foreach($this->decisionVoters as $voter) {
			if(!$voter->supportsClass($className)) {
				return false;
			}
		}
		return true;
	}
}