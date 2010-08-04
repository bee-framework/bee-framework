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

abstract class Bee_Security_Vote_AbstractDecisionManager implements Bee_Security_IAccessDecisionManager, Bee_Context_Config_IInitializingBean {

	/**
	 * 
	 * @var Bee_Security_Vote_IAccessDecisionVoter[]
	 */
	private $decisionVoters;

	/**
	 * 
	 * @var boolean
	 */
	private $allowIfAllAbstainDecisions;

	public function afterPropertiesSet() {
		Bee_Utils_Assert::isTrue(count($this->decisionVoters) > 0, 'A list of AccessDecisionVoters is required');
	}

	protected final function checkAllowIfAllAbstainDecisions() {
        if (!$this->isAllowIfAllAbstainDecisions()) {
            throw new Bee_Security_Exception_AccessDenied('access_denied'); // @todo: message source
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

    public function getDecisionVoters() {
    	return $this->decisionVoters;
    }

    public function setDecisionVoters(array $decisionVoters) {
    	foreach($decisionVoters as $voter) {
    		Bee_Utils_Assert::isInstanceOf('Bee_Security_Vote_IAccessDecisionVoter', $voter, 'AccessDecisionVoter ' . get_class($voter) . ' must implement Bee_Security_Vote_IAccessDecisionVoter');
    	}
    	$this->decisionVoters = $decisionVoters; 
    }

	public function supports(Bee_Security_IConfigAttribute $configAttribute) {
		foreach($this->decisionVoters as $voter) {
			if($voter->supports($configAttribute)) {
				return true;
			}
		}
		return false;
	}

	public function supportsClass($className) {
		foreach($this->decisionVoters as $voter) {
			if(!$voter->supportsClass($className)) {
				return false;
			}
		}
		return true;
	}
}
?>