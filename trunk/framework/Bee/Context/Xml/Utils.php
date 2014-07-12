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
use Bee\Context\Config\IBeanDefinition;

/**
 * User: mp
 * Date: 04.07.11
 * Time: 00:21
 */
class Bee_Context_Xml_Utils implements Bee_Context_Xml_IConstants {

    /**
     * @param DOMElement $ele
     * @return null|string
     */
    public static function parseParentAttribute(DOMElement $ele) {
        $parent = null;
        if ($ele->hasAttribute(self::PARENT_ATTRIBUTE)) {
            $parent = $ele->getAttribute(self::PARENT_ATTRIBUTE);
            return $parent;
        }
        return $parent;
    }

	/**
	 * @param DOMElement $ele
	 * @param IBeanDefinition $bd
	 */
	public static function parseDependsOnAttribute(DOMElement $ele, IBeanDefinition $bd) {
        if ($ele->hasAttribute(self::DEPENDS_ON_ATTRIBUTE)) {
            $dependsOn = $ele->getAttribute(self::DEPENDS_ON_ATTRIBUTE);
            $bd->setDependsOn(Bee_Utils_Strings::tokenizeToArray($dependsOn, self::BEAN_NAME_DELIMITERS));
        }
    }

	/**
	 * @param DOMElement $ele
	 * @return array|null
	 */
	public static function parseNameAttribute(DOMElement $ele) {
        if($ele->hasAttribute(self::NAME_ATTRIBUTE)) {
            $nameAttr = $ele->getAttribute(self::NAME_ATTRIBUTE);
            return Bee_Utils_Strings::tokenizeToArray($nameAttr, self::BEAN_NAME_DELIMITERS);
        }
        return null;
    }

	/**
	 * @param array $aliases
	 * @param Bee_Context_Xml_ReaderContext $readerContext
	 * @param DOMElement $ele
	 * @return mixed
	 */
	public static function getIdFromAliases(array &$aliases, Bee_Context_Xml_ReaderContext $readerContext, DOMElement $ele) {
        $beanName = array_shift($aliases);
        $readerContext->notice("No XML 'id' specified - using '$beanName' as bean name and [".implode(', ', $aliases)."] as aliases", $ele);
        return $beanName;
    }

	/**
	 * @param DOMElement $ele
	 * @param IBeanDefinition $bd
	 * @param IBeanDefinition $containingBd
	 */
	public static function parseScopeAttribute(DOMElement $ele, IBeanDefinition $bd, IBeanDefinition $containingBd = null) {
        if ($ele->hasAttribute(self::SCOPE_ATTRIBUTE)) {
            $bd->setScope($ele->getAttribute(self::SCOPE_ATTRIBUTE));
        } else if (!is_null($containingBd)) {
            // Take default from containing bean in case of an inner bean definition.
            $bd->setScope($containingBd->getScope());
        }
    }
}
