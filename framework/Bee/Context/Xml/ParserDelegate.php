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
 * Enter description here...
 *
 * @todo: What's with the various undefined $defaultTypeClassName vars littered throughout this class? Refer to Source (i.e. Spring) and fix...
 * 
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class Bee_Context_Xml_ParserDelegate {
	
	/**
	 * The default namespace for bean definitions
	 *
	 * @var String
	 */
	const BEANS_NAMESPACE_URI = 'http://www.beeframework.org/schema/beans';

	const BEAN_NAME_DELIMITERS = ',; ';

	const TRUE_VALUE = 'true';
	
	const DESCRIPTION_ELEMENT = 'description';

	const BEAN_ELEMENT = 'bean';
	
	const ID_ATTRIBUTE = 'id';
	
	const NAME_ATTRIBUTE = 'name';
	
	const CLASS_ATTRIBUTE = 'class';
	
	const PARENT_ATTRIBUTE = 'parent';
	
	const SCOPE_ATTRIBUTE = 'scope';
	
	const ABSTRACT_ATTRIBUTE = 'abstract';

	const DEPENDS_ON_ATTRIBUTE = 'depends-on';
	
	const INIT_METHOD_ATTRIBUTE = 'init-method';

	const DESTROY_METHOD_ATTRIBUTE = 'destroy-method';

	const FACTORY_METHOD_ATTRIBUTE = 'factory-method';

	const FACTORY_BEAN_ATTRIBUTE = 'factory-bean';
	
	const CONSTRUCTOR_ARG_ELEMENT = 'constructor-arg';

	const INDEX_ATTRIBUTE = 'index';

	const TYPE_ATTRIBUTE = 'type';

	const VALUE_TYPE_ATTRIBUTE = 'value-type';

	const PROPERTY_ELEMENT = 'property';

	const REF_ATTRIBUTE = 'ref';

	const VALUE_ATTRIBUTE = 'value';

	const REF_ELEMENT = 'ref';

	const IDREF_ELEMENT = 'idref';

	const BEAN_REF_ATTRIBUTE = 'bean';

	const LOCAL_REF_ATTRIBUTE = "local";

	const PARENT_REF_ATTRIBUTE = 'parent';

	const VALUE_ELEMENT = 'value';

	const NULL_ELEMENT = 'null';
	
	const LIST_ELEMENT = 'list';
	
	const ASSOC_ARRAY_ELEMENT = 'assoc-array';

	const ASSOC_ITEM_ELEMENT = 'assoc-item';
	
	const KEY_ATTRIBUTE = 'key';
	
	const DEFAULT_MERGE_ATTRIBUTE = 'default-merge';

	const DEFAULT_INIT_METHOD_ATTRIBUTE = 'default-init-method';

	const DEFAULT_DESTROY_METHOD_ATTRIBUTE = 'default-destroy-method';
	
	
	/**
	 * Enter description here...
	 *
	 * @var Bee_Context_Xml_DocumentDefaultsDefinition
	 */
	private $defaults;
	
	/**
	 * Enter description here...
	 *
	 * @var Bee_Context_Xml_ReaderContext
	 */
	private $readerContext;
	
	
	/**
	 * Enter description here...
	 *
	 * @var array
	 */
	private $usedNames = array();
	
	/**
	 * Enter description here...
	 *
	 * @var array
	 */
	private $parseState = array();
	
	
	/**
	 * Enter description here...
	 *
	 * @param Bee_Context_Xml_ReaderContext $readerContext
	 */
	public function __construct(Bee_Context_Xml_ReaderContext $readerContext) {
		Bee_Utils_Assert::notNull($readerContext, 'XmlReaderContext must not be null');
		$this->readerContext = $readerContext;		
	}
	
	
	public function initDefaults(DOMElement $root) {
		$defaults = new Bee_Context_Xml_DocumentDefaultsDefinition();
		$defaults->setMerge($root->getAttribute(self::DEFAULT_MERGE_ATTRIBUTE));
		if($root->hasAttribute(self::DEFAULT_INIT_METHOD_ATTRIBUTE)) {
			$defaults->setInitMethod($root->getAttribute(self::DEFAULT_INIT_METHOD_ATTRIBUTE));
		}
		if($root->hasAttribute(self::DEFAULT_DESTROY_METHOD_ATTRIBUTE)) {
			$defaults->setDestroyMethod($root->getAttribute(self::DEFAULT_DESTROY_METHOD_ATTRIBUTE));
		}
		
		// @todo: provide source info via BeanMetadataElement
//		defaults.setSource(this.readerContext.extractSource(root));

		$this->defaults = $defaults;
	}
	
	
	/**
	 * Enter description here...
	 *
	 * @param DOMElement $ele
	 * @return Bee_Context_Config_BeanDefinitionHolder
	 */
	public function parseBeanDefinitionElement(DOMElement $ele, Bee_Context_Config_IBeanDefinition $containingBd = null) {
		$id = $ele->getAttribute(self::ID_ATTRIBUTE);
		$nameAttr = $ele->getAttribute(self::NAME_ATTRIBUTE);

		$aliases = Bee_Utils_Strings::tokenizeToArrayKeys($nameAttr, self::BEAN_NAME_DELIMITERS);
		$aliases_keys = array_keys($aliases);

		// find a bean id
		$beanName = $id;
		if (!Bee_Utils_Strings::hasText($beanName) && count($aliases) > 0) {
			$beanName = array_shift($aliases_keys);
			unset($aliases[$beanName]);
			$this->readerContext->notice("No XML 'id' specified - using '$beanName' as bean name and $aliases as aliases", $ele);
		}

		if (is_null($containingBd)) {
			$this->checkNameUniqueness($beanName, $aliases, $ele);
		}
		
		$beanDefinition = $this->parseNamedBeanDefinitionElement($ele, $beanName, $containingBd);

		if (!is_null($beanDefinition)) {
			if (!Bee_Utils_Strings::hasText($beanName)) {
				try {
					$beanName = Bee_Context_Support_BeanDefinitionReaderUtils::generateBeanName($beanDefinition, $this->readerContext->getRegistry(), ($containingBean != null));
					$this->readerContext->notice("Neither XML 'id' nor 'name' specified - using generated bean name [$beanName]", $ele);
				} catch (Exception $ex) {
					$this->readerContext->error($ex->getMessage(), $ele, $ex);
					return null;
				}
			}
			
			return new Bee_Context_Config_BeanDefinitionHolder($beanDefinition, $beanName, $aliases_keys);
		}
		return null;
	}

	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $beanName
	 * @param array $aliases
	 * @param DOMElement $beanElement
	 */
	private function checkNameUniqueness($beanName, array $aliases, DOMElement $beanElement) {
		$foundName = null;

		if (Bee_Utils_Strings::hasText($beanName) && array_key_exists($beanName, $this->usedNames)) {
			$foundName = $beanName;
		}
		if (is_null($foundName)) {
			$foundName = Bee_Utils_Collections::findFirstKeyMatch($this->usedNames, $aliases);
		}
		if (!is_null($foundName)) {
			$this->readerContext->error("Bean name '$foundName' is already used in this file", $beanElement);
		}

		$this->usedNames[$beanName] = true;
		$this->usedNames = array_merge($this->usedNames, $aliases);
	}	


	/**
	 * Enter description here...
	 *
	 * @param DOMElement $ele
	 * @param unknown_type $beanName
	 * @param Bee_Context_Config_IBeanDefinition $containingBd
	 * @return Bee_Context_Config_BeanDefinition_Abstract
	 */
	public function parseNamedBeanDefinitionElement(DOMElement $ele, $beanName, Bee_Context_Config_IBeanDefinition $containingBd = null) {
		$className = null;
		if ($ele->hasAttribute(self::CLASS_ATTRIBUTE)) {
			$className = trim($ele->getAttribute(self::CLASS_ATTRIBUTE));
		}
		$parent = null;
		if ($ele->hasAttribute(self::PARENT_ATTRIBUTE)) {
			$parent = $ele->getAttribute(self::PARENT_ATTRIBUTE);
		}

		try {
			array_push($this->parseState, $beanName);

			$bd = Bee_Context_Support_BeanDefinitionReaderUtils::createBeanDefinition($parent, $className);

			if ($ele->hasAttribute(self::SCOPE_ATTRIBUTE)) {
				$bd->setScope($ele->getAttribute(self::SCOPE_ATTRIBUTE));
			} else if (!is_null($containingBd)) {
				// Take default from containing bean in case of an inner bean definition.
				$bd->setScope($containingBd->getScope());
			}

			if ($ele->hasAttribute(self::ABSTRACT_ATTRIBUTE)) {
				$bd->setAbstract(self::TRUE_VALUE === $ele->getAttribute(self::ABSTRACT_ATTRIBUTE));
			}
			
			if ($ele->hasAttribute(self::DEPENDS_ON_ATTRIBUTE)) {
				$dependsOn = $ele->getAttribute(self::DEPENDS_ON_ATTRIBUTE);
				$bd->setDependsOn(Bee_Utils_Strings::tokenizeToArray($dependsOn, self::BEAN_NAME_DELIMITERS));
			}

			if ($ele->hasAttribute(self::INIT_METHOD_ATTRIBUTE)) {
				$initMethodName = $ele->getAttribute(self::INIT_METHOD_ATTRIBUTE);
				if (Bee_Utils_Strings::hasText($initMethodName)) {
					$bd->setInitMethodName($initMethodName);
				}
			} else {
				$initMethodName = $this->defaults->getInitMethod();
				if (Bee_Utils_Strings::hasText($initMethodName)) {
					$bd->setInitMethodName($initMethodName);
					$bd->setEnforceInitMethod(false);
				}
			}

			if ($ele->hasAttribute(self::DESTROY_METHOD_ATTRIBUTE)) {
				$destroyMethodName = $ele->getAttribute(self::DESTROY_METHOD_ATTRIBUTE);
				if (Bee_Utils_Strings::hasText($destroyMethodName)) {
					$bd->setDestroyMethodName($destroyMethodName);
				}
			} else {
				$destroyMethodName = $this->defaults->getDestroyMethod();
				if (Bee_Utils_Strings::hasText($destroyMethodName)) {
					$bd->setDestroyMethodName($destroyMethodName);
					$bd->setEnforceDestroyMethod(false);
				}
			}

			if ($ele->hasAttribute(self::FACTORY_METHOD_ATTRIBUTE)) {
				$bd->setFactoryMethodName($ele->getAttribute(self::FACTORY_METHOD_ATTRIBUTE));
			}
			if ($ele->hasAttribute(self::FACTORY_BEAN_ATTRIBUTE)) {
				$bd->setFactoryBeanName($ele->getAttribute(self::FACTORY_BEAN_ATTRIBUTE));
			}

//			bd.setDescription(DomUtils.getChildElementValueByTagName(ele, DESCRIPTION_ELEMENT));

//			$this->parseMetaElements(ele, bd);
//			parseLookupOverrideSubElements(ele, bd.getMethodOverrides());
//			parseReplacedMethodSubElements(ele, bd.getMethodOverrides());

			$this->parseConstructorArgElements($ele, $bd);
			$this->parsePropertyElements($ele, $bd);

//			bd.setResource(this.readerContext.getResource());
//			bd.setSource(extractSource(ele));

			array_pop($this->parseState);
			return $bd;

//		} catch (ClassNotFoundException ex) {
//			error("Bean class [" + className + "] not found", ele, ex);
//		} catch (NoClassDefFoundError err) {
//			error("Class that bean class [" + className + "] depends on not found", ele, err);
		} catch (Exception $ex) {
			array_pop($this->parseState);
			$this->readerContext->error("Unexpected failure during bean definition parsing: $ex->getMessage()", $ele, $ex);
		}

		array_pop($this->parseState);
		return null;
	}
	

	/**
	 * Parse constructor-arg sub-elements of the given bean element.
	 *
	 * @param DOMElement $beanEle
	 * @param Bee_Context_Config_IBeanDefinition $bd
	 * @return void
	 */
	public function parseConstructorArgElements(DOMElement $beanEle, Bee_Context_Config_IBeanDefinition $bd) {
		$nl = $beanEle->childNodes;
		foreach($nl as $node) {
			if ($node instanceof DOMElement && Bee_Utils_Dom::nodeNameEquals($node, self::CONSTRUCTOR_ARG_ELEMENT)) {
				$this->parseConstructorArgElement($node, $bd);
			}
		}
	}

	
	/**
	 * Parse property sub-elements of the given bean element.
	 *
	 * @param DOMElement $beanEle
	 * @param Bee_Context_Config_IBeanDefinition $bd
	 * @return void
	 */
	public function parsePropertyElements(DOMElement $beanEle, Bee_Context_Config_IBeanDefinition $bd) {
		$nl = $beanEle->childNodes;
		foreach($nl as $node) {
			if ($node instanceof DOMElement && Bee_Utils_Dom::nodeNameEquals($node, self::PROPERTY_ELEMENT)) {
				$this->parsePropertyElement($node, $bd);
			}
		}
	}

	
	/**
	 * Parse a constructor-arg element.
	 */
	public function parseConstructorArgElement(DOMElement $ele, Bee_Context_Config_IBeanDefinition $bd) {
		
		$indexAttr = $ele->getAttribute(self::INDEX_ATTRIBUTE);
		$typeAttr = $ele->getAttribute(self::TYPE_ATTRIBUTE);

		if (Bee_Utils_Strings::hasLength($indexAttr) && is_numeric($indexAttr) && ($index = intval($indexAttr)) >= 0) {
			$existingArgs = $bd->getConstructorArgumentValues(); 
			if(isset($existingArgs[$index])) {
				$this->readerContext->error("Multiple occurrences of value $index for attribute 'index' of tag 'constructor-arg'");
			} else {
				try {
					array_push($this->parseState, "Constructor_Arg_Idx_$index");
					$value = $this->parsePropertyValue($ele, $bd, null);
					$valueHolder = new Bee_Beans_PropertyValue($index, $value, Bee_Utils_Strings::hasLength($typeAttr) ? $typeAttr : null);
					$bd->addConstructorArgumentValue($valueHolder);
					array_pop($this->parseState);
				} catch (Exception $ex) {
					array_pop($this->parseState);
					throw $ex;
				}
			}
		} else {
			$this->readerContext->error("Attribute 'index' of tag 'constructor-arg' is required, must be an integer and not lower than 0 (is '$indexAttr')", $ele);
		}
	}

	/**
	 * Parse a property element.
	 */
	public function parsePropertyElement(DOMElement $ele, Bee_Context_Config_IBeanDefinition $bd) {
		
		$propertyName = $ele->getAttribute(self::NAME_ATTRIBUTE);
		if (!Bee_Utils_Strings::hasText($propertyName)) {
			$this->readerContext->error("Tag 'property' must have a 'name' attribute", $ele);
			return;
		}
		array_push($this->parseState, $propertyName);
		try {
			if (array_key_exists($propertyName, $bd->getPropertyValues())) {
				$this->readerContext->error("Multiple 'property' definitions for property '$propertyName'", $ele);
				return;
			}
			$val = $this->parsePropertyValue($ele, $bd, $propertyName);
			$pv = new Bee_Beans_PropertyValue($propertyName, $val);
//			$pv->setSource(extractSource(ele));

			$bd->addPropertyValue($pv);

			array_pop($this->parseState);
		} catch (Exception $ex) {
			array_pop($this->parseState);
			throw $ex;
		}
	}

	
	/**
	 * Get the value of a property element. May be a list etc.
	 * Also used for constructor arguments, "propertyName" being null in this case.
	 */
	public function parsePropertyValue(DOMElement $ele, Bee_Context_Config_IBeanDefinition $bd, $propertyName) {
		$elementName = ($propertyName != null) ?
						"<property> element for property '$propertyName'" : "<constructor-arg> element";
		return $this->parseComplexPropElement($ele, $bd, $elementName);
	}
	
	private function parseComplexPropElement(DOMElement $ele, Bee_Context_Config_IBeanDefinition $bd, $elementName) {
		
		// Should only have one child element: ref, value, list, etc.
		$nl = $ele->childNodes;
		$subElement = null;
		foreach($nl as $candidateEle) {
			if ($candidateEle instanceof DOMElement) {
				if (Bee_Utils_Dom::nodeNameEquals($candidateEle, self::DESCRIPTION_ELEMENT)) {
					// Keep going: we don't use these values for now.
				} else {
					// Child element is what we're looking for.
					if (!is_null($subElement)) {
						$this->readerContext->error("$elementName must not contain more than one sub-element", $ele);
					} else {
						$subElement = $candidateEle;
					}
				}
			}
		}

		$hasRefAttribute = $ele->hasAttribute(self::REF_ATTRIBUTE);
		$hasValueAttribute = $ele->hasAttribute(self::VALUE_ATTRIBUTE);
		if (($hasRefAttribute && $hasValueAttribute) || (($hasRefAttribute || $hasValueAttribute) && !is_null($subElement))) {
			$this->readerContext->error("$elementName is only allowed to contain either 'ref' attribute OR 'value' attribute OR sub-element", $ele);
		}

		if ($hasRefAttribute) {
			$refName = $ele->getAttribute(self::REF_ATTRIBUTE);
			if (!Bee_Utils_Strings::hasText($refName)) {
				$this->readerContext->error("$elementName contains empty 'ref' attribute", $ele);
			}
			$ref = new Bee_Context_Config_RuntimeBeanReference($refName);
			// @todo provide source info via BeanMetadataElement
//			ref.setSource(extractSource(ele));
			return $ref;
		} else if ($hasValueAttribute) {
			
			// @todo: make it possible to set a type attribute on the element?
			$valueHolder = new Bee_Context_Config_TypedStringValue($ele->getAttribute(self::VALUE_ATTRIBUTE), $defaultTypeClassName);
			// @todo provide source info via BeanMetadataElement
//			valueHolder.setSource(extractSource(ele));
			return $valueHolder;
		} else if (!is_null($subElement)) {
			return $this->parsePropertySubElement($subElement, $bd);
		} else {
			// Neither child element nor "ref" or "value" attribute found.
			$this->readerContext->error("$elementName must specify a ref or value", $ele);
			return null;
		}
	}

	
	
	/**
	 * Parse a value, ref or collection sub-element of a property or
	 * constructor-arg element.
	 *
	 * @param DOMElement $ele subelement of property element; we don't know which yet
	 * @param Bee_Context_Config_IBeanDefinition $bd
	 * @param String $defaultTypeClassName the default type (class name) for any
	 * <code>&lt;value&gt;</code> tag that might be created
	 * 
	 * @return
	 */
	public function parsePropertySubElement(DOMElement $ele, Bee_Context_Config_IBeanDefinition $bd) {

		if (!$this->isDefaultNamespace($ele->namespaceURI)) {

			return $this->parseNestedCustomElement($ele, $bd);

		} else if (Bee_Utils_Dom::nodeNameEquals($ele, self::BEAN_ELEMENT)) {

			$bdHolder = $this->parseBeanDefinitionElement($ele, $bd);
			if (!is_null($bdHolder)) {
				$bdHolder = $this->decorateBeanDefinitionIfRequired($ele, $bdHolder);
			}
			return $bdHolder;

		} else if (Bee_Utils_Dom::nodeNameEquals($ele, self::REF_ELEMENT)) {

			// A generic reference to any name of any bean.
			$refName = $ele->getAttribute(self::BEAN_REF_ATTRIBUTE);
			$toParent = false;
			if (!Bee_Utils_Strings::hasLength($refName)) {
				// A reference to the id of another bean in the same XML file.
				$refName = $ele->getAttribute(self::LOCAL_REF_ATTRIBUTE);
				if (!Bee_Utils_Strings::hasLength($refName)) {
					// A reference to the id of another bean in a parent context.
					$refName = $ele->getAttribute(self::PARENT_REF_ATTRIBUTE);
					$toParent = true;
					if (!Bee_Utils_Strings::hasLength($refName)) {
						$this->readerContext->error("'bean', 'local' or 'parent' is required for <ref> element", $ele);
						return null;
					}
				}
			}
			if (!Bee_Utils_Strings::hasText($refName)) {
				$this->readerContext->error("<ref> element contains empty target attribute", $ele);
				return null;
			}
			$ref = new Bee_Context_Config_RuntimeBeanReference($refName, $toParent);
			// @todo provide source info via BeanMetadataElement
//			ref.setSource(extractSource(ele));
			return $ref;

		} else if (Bee_Utils_Dom::nodeNameEquals($ele, self::IDREF_ELEMENT)) {
			
			// A generic reference to any name of any bean.
			$refName = $ele->getAttribute(self::BEAN_REF_ATTRIBUTE);
			if (!Bee_Utils_Strings::hasLength($refName)) {
				// A reference to the id of another bean in the same XML file.
				$refName = $ele->getAttribute(self::LOCAL_REF_ATTRIBUTE);
				if (!Bee_Utils_Strings::hasLength($refName)) {
					$this->readerContext->error("Either 'bean' or 'local' is required for <idref> element", $ele);
					return null;
				}
			}
			if (!Bee_Utils_Strings::hasText($refName)) {
				$this->readerContext->error("<idref> element contains empty target attribute", $ele);
				return null;
			}
			$ref = new Bee_Context_Config_RuntimeBeanNameReference($refName);
			// @todo provide source info via BeanMetadataElement
//			$ref->setSource(extractSource(ele));
			return $ref;

		} else if (Bee_Utils_Dom::nodeNameEquals($ele, self::VALUE_ELEMENT)) {

			// It's a literal value.
			$value = Bee_Utils_Dom::getTextValue($ele);

			$typeClassName = $ele->getAttribute(self::TYPE_ATTRIBUTE);
			if (!Bee_Utils_Strings::hasText($typeClassName)) {
				$typeClassName = $defaultTypeClassName;
			}
//			try {
				return $this->buildTypedStringValue($value, $typeClassName, $ele);
//			} catch (ClassNotFoundException ex) {
//				error("Type class [" + typeClassName + "] not found for <value> element", ele, ex);
//				return value;
//			}

		} else if (Bee_Utils_Dom::nodeNameEquals($ele, self::NULL_ELEMENT)) {

			// It's a distinguished null value. Let's wrap it in a TypedStringValue
			// object in order to preserve the source location.
			$nullHolder = new Bee_Context_Config_TypedStringValue(null);
			// @todo provide source info via BeanMetadataElement
//			$nullHolder->setSource(extractSource(ele));
			return $nullHolder;

			// @todo: determine sensible collection types for PHP and implement parsers accordingly...
		} else if (Bee_Utils_Dom::nodeNameEquals($ele, self::LIST_ELEMENT)) {
			
			return $this->parseListElement($ele, $bd);
		
		} else if (Bee_Utils_Dom::nodeNameEquals($ele, self::ASSOC_ARRAY_ELEMENT)) {
			
			return $this->parseAssocArrayElement($ele, $bd);
		
//		} else if (Bee_Utils_Dom::nodeNameEquals($ele, SET_ELEMENT)) {
//
//			return $this->parseSetElement($ele, $bd);
//
//		} else if (Bee_Utils_Dom::nodeNameEquals($ele, MAP_ELEMENT)) {
//
//			return $this->parseMapElement($ele, $bd);
//
//		} else if (Bee_Utils_Dom::nodeNameEquals($ele, PROPS_ELEMENT)) {
//
//			return $this->parsePropsElement($ele);

		}
		$this->readerContext->error("Unknown property sub-element: [$ele->nodeName]", $ele);
		return null;
	}
	
	
	/**
	 * Build a typed String value Object for the given raw value.
	 * @see org.springframework.beans.factory.config.TypedStringValue
	 *
	 * @param String $value
	 * @param String $targetTypeName
	 * @param DOMElement $ele
	 * @return Bee_Context_Config_TypedStringValue
	 */
	protected function buildTypedStringValue($value, $targetTypeName, DOMElement $ele) {
		$typedValue = null;
		if (!Bee_Utils_Strings::hasText($targetTypeName)) {
			$typedValue = new Bee_Context_Config_TypedStringValue($value);
		} else {
			$typedValue = new Bee_Context_Config_TypedStringValue($value, $targetTypeName);
		}
		// @todo provide source info via BeanMetadataElement
//		$typedValue->setSource(extractSource(ele));
		return $typedValue;
	}

	
	/**
	 * Parse a list element.
	 *
	 * @param DOMElement $collectionEle
	 * @param Bee_Context_Config_IBeanDefinition $bd
	 * @return array
	 */
	public function parseListElement(DOMElement $collectionEle, Bee_Context_Config_IBeanDefinition $bd) {
		$defaultTypeClassName = $collectionEle->getAttribute(self::VALUE_TYPE_ATTRIBUTE);

		// @todo: what about that managed list stuff?
		$nl = $collectionEle->childNodes;
		$list = array();
		foreach($nl as $ele) {
			if($ele instanceof DOMElement) {
				array_push($list, $this->parsePropertySubElement($ele, $bd, $defaultTypeClassName));
			}
		}
		return $list;
	}

	
	public function parseAssocArrayElement(DOMElement $collectionEle, Bee_Context_Config_IBeanDefinition $bd) {
		$defaultTypeClassName = $collectionEle->getAttribute(self::VALUE_TYPE_ATTRIBUTE);
		// @todo: what about that managed list stuff?
		$nl = $collectionEle->childNodes;
		$list = array();
		foreach($nl as $ele) {
			if($ele instanceof DOMElement) {
				$list = array_merge($list, $this->parseAssocItemElement($ele, $bd, $defaultTypeClassName));
			}
		}
		return $list;
	}

	
	
	public function parseAssocItemElement(DOMElement $ele, Bee_Context_Config_IBeanDefinition $bd, $defaultTypeClassName) {
		Bee_Utils_Assert::isTrue(Bee_Utils_Dom::nodeNameEquals($ele, self::ASSOC_ITEM_ELEMENT), 'Tag assoc-array must not contain elements other than assoc-item');

		$key = $ele->getAttribute(self::KEY_ATTRIBUTE);
		if (!Bee_Utils_Strings::hasText($key)) {
			$this->readerContext->error("Tag 'assoc-item' must have a 'key' attribute", $ele);
			return;
		}
		$val = $this->parseComplexPropElement($ele, $bd, "<assoc-item> for key $key");
		return array($key => $val);
	}

	
	
	/**
	 * Enter description here...
	 *
	 * @param DOMElement $root
	 * @return Bee_Context_Config_IBeanDefinition
	 */
	public function parseCustomElement(DOMElement $ele, Bee_Context_Config_IBeanDefinition $containingBd = null) {
		$namespaceUri = $ele->namespaceURI;
		$handler = $this->readerContext->getNamespaceHandlerResolver()->resolve($namespaceUri);
		if (is_null($handler)) {
			$this->readerContext->error("Unable to locate Spring NamespaceHandler for XML schema namespace [$namespaceUri]", $ele);
			return null;
		}
		return $handler->parse($ele, new Bee_Context_Xml_ParserContext($this->readerContext, $this, $containingBd));
	}

	
	/**
	 * Enter description here...
	 *
	 * @param DOMElement $ele
	 * @param Bee_Context_Config_BeanDefinitionHolder $bdHolder
	 * @return Bee_Context_Config_BeanDefinitionHolder
	 */
	public function decorateBeanDefinitionIfRequired(DOMElement $ele, Bee_Context_Config_BeanDefinitionHolder $definitionHolder) {
		$finalDefinition = $definitionHolder;

		// Decorate based on custom attributes first.
		$attributes = $ele->attributes;
		foreach($attributes as $node) {
			$finalDefinition = $this->decorateIfRequired($node, $finalDefinition);			
		}

		// Decorate based on custom nested elements.
		$children = $ele->childNodes;
		foreach($children as $node) {
			if ($node->nodeType == XML_ELEMENT_NODE) {
				$finalDefinition = $this->decorateIfRequired($node, $finalDefinition);
			}
		}
		return $finalDefinition;
	}

	/**
	 * Enter description here...
	 *
	 * @param DOMNode $node
	 * @param Bee_Context_Config_BeanDefinitionHolder $originalDefinition
	 * @return Bee_Context_Config_BeanDefinitionHolder
	 */
	private function decorateIfRequired(DOMNode $node, Bee_Context_Config_BeanDefinitionHolder $originalDefinition) {
		$namespaceUri = $node->namespaceURI;
		if (!$this->isDefaultNamespace($namespaceUri)) {
			$handler = $this->readerContext->getNamespaceHandlerResolver()->resolve($namespaceUri);
			if (!is_null($handler)) {
				return $handler->decorate($node, $originalDefinition, new Bee_Context_Xml_ParserContext($this->readerContext, $this));
			} else if (Bee_Utils_Strings::startsWith($namespaceUri, 'http://www.beeframework.org/')) {
				$this->readerContext->error("Unable to locate Bee NamespaceHandler for XML schema namespace [$namespaceUri]", $node);
			} else {
				// A custom namespace, not to be handled by Spring - maybe "xml:...".
//				if (logger.isDebugEnabled()) {
//					$this->readerContext->error("No Bee NamespaceHandler found for XML schema namespace [$namespaceUri]", $node);
//				}
			}
		}
		return $originalDefinition;
	}

	
	/**
	 * Enter description here...
	 *
	 * @param String $namespaceUri
	 * @return boolean
	 */
	public function isDefaultNamespace($namespaceUri) {
		return (!Bee_Utils_Strings::hasLength($namespaceUri) || self::BEANS_NAMESPACE_URI === $namespaceUri);
	}
}
?>