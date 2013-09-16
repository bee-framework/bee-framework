<?php
/*
 * Copyright 2008-2013 the original author or authors.
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
 * Smarty
 *
 */
/**
 * Smarty block displayed if the currently authenticated user has a given role.
 * Parameters:
 * - all (string) - comma-separated list of roles the user is required to have
 * - any (string) - comma-separated list of roles the user may have
 * - none (string) - comma-separated list of roles the user must not have
 * @param $params
 * @param $content
 * @param $smarty
 * @param $repeat
 * @return mixed
 */
function smarty_block_bee_auth_has_role ($params, $content, &$smarty, &$repeat) {
	if($repeat) {
		$roles = Bee_Security_Helper::getRoles();

		$requiredRoles = array_filter(array_map('trim', explode(',', $params['all'])));
		$possibleRoles = array_filter(array_map('trim', explode(',', $params['any'])));
		$forbiddenRoles = array_filter(array_map('trim', explode(',', $params['none'])));

		// check if all required roles present
		$repeat = array_intersect($roles, $requiredRoles) === $requiredRoles;

		// check if any of the possible roles are present
		$repeat = $repeat && (count($possibleRoles) > 0 ? count(array_intersect($roles, $possibleRoles)) > 0 : true);

		// check that none of the forbidden roles are present
		$repeat = $repeat && count(array_intersect($roles, $forbiddenRoles)) === 0;
	}
	return $content;
}
 