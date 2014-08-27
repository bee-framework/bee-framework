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
 */
interface Bee_Context_Xml_Namespace_IBeanDefinitionDecorator {

	/**
	 * Parse the specified {@link DOMNode} (either an element or an attribute) and decorate
	 * the supplied {@link Bee_Context_Config_BeanDefinitionHolder}, returning the decorated definition.
	 * <p>Implementations may choose to return a completely new definition, which will
	 * replace the original definition.
	 * <p>The supplied {@link Bee_Context_Xml_ParserContext} can be used to register any additional
	 * beans needed to support the main definition.
	 *
	 * @param DOMNode $node
	 * @param Bee_Context_Config_BeanDefinitionHolder $definition
	 * @param Bee_Context_Xml_ParserContext $parserContext
	 * @return Bee_Context_Config_BeanDefinitionHolder
	 */
	function decorate(DOMNode $node, Bee_Context_Config_BeanDefinitionHolder $definition, Bee_Context_Xml_ParserContext $parserContext);
	
}
?>