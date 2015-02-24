<?php
namespace Bee\Utils;
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
use DOMCharacterData;
use DOMComment;
use DOMElement;
use DOMEntityReference;
use DOMNode;

/**
 * Enter description here...
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class Dom {
	
    /**
     * Retrieve all child elements of the given DOM element that match any of
     * the given element names. Only look at the direct child level of the
     * given element; do not go into further depth (in contrast to the
     * DOM API's <code>getElementsByTagName</code> method).
     * @param DOMElement $ele the DOM element to analyze
     * @param array $childEleNames the child element names to look for
     * @return DOMElement[] an array of child <code>DOMElement</code> instances
     * @see org.w3c.dom.Element
     * @see org.w3c.dom.Element#getElementsByTagName
     */
    public static function getChildElementsByTagNames(DOMElement $ele, array $childEleNames) {
        $childEles = array();
        foreach ($ele->childNodes as $node) {
            if ($node instanceof DOMElement && self::nodeNameMatchMulti($node, $childEleNames)) {
                $childEles[] = $node;
            }
        }
        return $childEles;
    }

    /**
     * Retrieve all child elements of the given DOM element that match
     * the given element name. Only look at the direct child level of the
     * given element; do not go into further depth (in contrast to the
     * DOM API's <code>getElementsByTagName</code> method).
     * @param DOMElement $ele the DOM element to analyze
     * @param string $childEleName the child element name to look for
     * @return DOMElement[] an array of child <code>DOMElement</code> instances
     * @see org.w3c.dom.Element
     * @see org.w3c.dom.Element#getElementsByTagName
     */
    public static function getChildElementsByTagName(DOMElement $ele, $childEleName) {
        $childEles = array();
        foreach ($ele->childNodes as $node) {
            if ($node instanceof DOMElement && self::nodeNameMatch($node, $childEleName)) {
                $childEles[] = $node;
            }
        }
        return $childEles;
    }

	/**
	 * Namespace-aware equals comparison. Returns <code>true</code> if either
	 * {@link Node#getLocalName} or {@link Node#getNodeName} equals <code>desiredName</code>,
	 * otherwise returns <code>false</code>.
	 *
	 * @param DOMNode $node
	 * @param String $desiredName
	 * @return boolean
	 */
	public static function nodeNameEquals(DOMNode $node, $desiredName) {
		Assert::notNull($node, 'Node must not be null');
		Assert::hasText($desiredName, 'Desired name must be set');
		return self::nodeNameMatch($node, $desiredName);
	}
	
	private static function nodeNameMatch(DOMNode $node, $desiredName) {
		return ($desiredName === $node->nodeName || $desiredName === $node->localName);
	}
	
	private static function nodeNameMatchMulti(DOMNode $node, array $desiredNames) {
		return (in_array($node->nodeName, $desiredNames) || in_array($node->localName, $desiredNames));
	}

	/**
	 * Extract the text value from the given DOM element, ignoring XML comments.
	 * <p>Appends all CharacterData nodes and EntityReference nodes
	 * into a single String value, excluding Comment nodes.
	 *
	 * @param DOMElement $valueEle
	 * @return String
	 */
	public static function getTextValue(DOMElement $valueEle) {
		Assert::notNull($valueEle, 'Element must not be null');
		$value = '';
		$nl = $valueEle->childNodes;
		foreach($nl as $node) {
			if (($node instanceof DOMCharacterData && !($node instanceof DOMComment)) || $node instanceof DOMEntityReference) {
				$value .= $node->nodeValue;
			}
		}
		return $value;
	}
}