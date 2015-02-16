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

use Bee\Security\ConfigAttributeDefinition;
use Bee\Security\Exception\AccessDeniedException;
use Bee\Security\IAuthentication;
use Bee\Security\Vote\IAccessDecisionVoter;

/**
 * Class AffirmativeBased
 * @package Bee\Security\Vote
 */
class AffirmativeBased extends AbstractDecisionManager {

	/**
	 * @param IAuthentication $authentication
	 * @param mixed $object
	 * @param ConfigAttributeDefinition $configAttributes
	 * @throws AccessDeniedException
	 */
	public function decide(IAuthentication $authentication, $object, ConfigAttributeDefinition $configAttributes) {
		$deny = 0;
		
		foreach($this->getDecisionVoters() as $voter) {
			$result = $voter->vote($authentication, $object, $configAttributes);

			switch($result) {
				case IAccessDecisionVoter::ACCESS_GRANTED:
					return;
				case IAccessDecisionVoter::ACCESS_DENIED:
					$deny++;
					break;
				default:
					break;
			}
		}

		if($deny > 0) {
			throw new AccessDeniedException('access_denied');
		}

        // To get this far, every AccessDecisionVoter abstained
        $this->checkAllowIfAllAbstainDecisions();
	}
}