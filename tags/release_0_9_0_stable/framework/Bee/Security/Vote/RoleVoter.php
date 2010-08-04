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
 * Votes if any {@link Bee_Security_IConfigAttribute#getAttribute()} starts with a prefix
 * indicating that it is a role. The default prefix string is <Code>ROLE_</code>,
 * but this may be overridden to any value. It may also be set to empty, which
 * means that essentially any attribute will be voted on. As described further
 * below, the effect of an empty prefix may not be quite desirable.
 * <p>
 * Abstains from voting if no configuration attribute commences with the role
 * prefix. Votes to grant access if there is an exact matching
 * {@link Bee_Security_IGrantedAuthority} to a <code>Bee_Security_IConfigAttribute</code>
 * starting with the role prefix. Votes to deny access if there is no exact
 * matching <code>Bee_Security_IGrantedAuthority</code> to a <code>Bee_Security_IConfigAttribute</code>
 * starting with the role prefix.
 * <p>
 * An empty role prefix means that the voter will vote for every
 * Bee_Security_IConfigAttribute. When there are different categories of Bee_Security_IConfigAttribute
 * used, this will not be optimal since the voter will be voting for attributes
 * which do not represent roles. However, this option may be of some use when
 * using pre-existing role names without a prefix, and no ability exists to
 * prefix them with a role prefix on reading them in, such as provided for
 * example in {@link org.springframework.security.userdetails.jdbc.JdbcDaoImpl}.
 * <p>
 * All comparisons and prefixes are case sensitive.
 * 
 */
class Bee_Security_Vote_RoleVoter implements Bee_Security_Vote_IAccessDecisionVoter {
	
	private $rolePrefix = "ROLE_";

	/**
	 * 
	 * @return string
	 */
	public function getRolePrefix() {
		return $this->rolePrefix;
	}

	/**
	 * 
	 * @param string $rolePrefix
	 * @return void
	 */
	public function setRolePrefix($rolePrefix) {
		$this->rolePrefix = $rolePrefix;
	}

	public function supports(Bee_Security_IConfigAttribute $configAttribute) {
		return Bee_Utils_Strings::startsWith($configAttribute->getAttribute(), $this->rolePrefix);
	}

	public function supportsClass($className) {
		return true;
	}

	public function vote(Bee_Security_IAuthentication $authentication, $object, Bee_Security_ConfigAttributeDefinition $config) {
        $result = self::ACCESS_ABSTAIN;

        $authorities = $this->extractAuthorities($authentication);
        foreach($config->getConfigAttributes() as $attribute) {
        	if($this->supports($attribute)) {
        		$result = self::ACCESS_DENIED;
        		$attrString = $attribute->getAttribute();
        		if(array_key_exists($attrString, $authorities)) {
        			return self::ACCESS_GRANTED;
        		}
        	}
        }

        return $result;
	}
	
	protected function extractAuthorities(Bee_Security_IAuthentication $authentication) {
		return $authentication->getAuthorities(); 
	}
}
?>