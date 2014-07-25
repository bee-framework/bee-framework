<?php
namespace Bee\Beans\PropertyEditor;
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
use Bee\Beans\IPropertyEditor;
use Bee\Utils\ITypeDefinitions;
use Bee_Context_BeanNotOfRequiredTypeException;
use Bee_Context_Config_IContextAware;
use Bee_Context_NoSuchBeanDefinitionException;
use Bee_IContext;
use Exception;

/**
 * Class PropertyEditorRegistry
 * @package Bee\Beans\PropertyEditor
 *
 * @author Benjamin Hartmann
 * @author Michael Plomer (michael.plomer@iter8.de)
 */
class PropertyEditorRegistry implements Bee_Context_Config_IContextAware {

	const PROPERTY_EDITOR_BEAN_NAME_PREFIX = 'propertyEditor_';

	/**
	 * @var Bee_IContext
	 */
	private $context;

	/**
	 * @var IPropertyEditor[]
	 */
	private static $staticallyRegisteredConverters = array();

	/**
	 * @param Bee_IContext $context
	 */
	function __construct(Bee_IContext $context = null) {
		$this->context = $context;
	}

	/**
	 *
	 * @param String $type
	 * @param IPropertyEditor $converter
	 */
	public static function registerEditor($type, IPropertyEditor $converter) {
		self::$staticallyRegisteredConverters[$type] = $converter;
	}

	/**
	 *
	 * @param string $type
	 * @throws \Exception
	 * @return IPropertyEditor
	 */
	public function getEditor($type) {
		if(array_key_exists($type, self::$staticallyRegisteredConverters)) {
			return self::$staticallyRegisteredConverters[$type];
		}
		return $this->getPropertyEditorBean($type);
	}

	/**
	 * Checks if a converter for the requested type is registered.
	 *
	 * @param String $type
	 * @return Boolean
	 */
	public function editorExists($type) {
		try {
			$this->getEditor($type);
			return true;
		} catch(PropertyEditorNotFoundException $e) {
			return false;
		}
	}

	/**
	 * Callback that supplies the owning context to a bean instance.
	 * <p>Invoked after the population of normal bean properties
	 * but before an initialization callback such as
	 * {@link InitializingBean#afterPropertiesSet()} or a custom init-method.
	 * @param Bee_IContext $context owning context (never <code>null</code>).
	 * The bean can immediately call methods on the context.
	 */
	public function setBeeContext(Bee_IContext $context) {
		$this->context = $context;
	}

	/**
	 * @param $type
	 * @throws PropertyEditorNotFoundException
	 * @return Object
	 */
	private function getPropertyEditorBean($type) {
		if(is_null($this->context)) {
			throw new PropertyEditorNotFoundException('The type "' . $type . '" has no statically registered type editors and dynamically registered editors are not yet available');
		}
		$failureReason = null;
		try {
			$idSafeTypeName = preg_replace('#\\\\#', '_', $type);
			return $this->context->getBean(self::PROPERTY_EDITOR_BEAN_NAME_PREFIX . $idSafeTypeName, 'Bee\Beans\IPropertyEditor');
		} catch(Bee_Context_NoSuchBeanDefinitionException $e) {
			$failureReason = $e;
		} catch(Bee_Context_BeanNotOfRequiredTypeException $e) {
			$failureReason = $e;
		}
		throw new PropertyEditorNotFoundException('The type "' . $type . '" has no registered type editors', 0, $failureReason);
	}
}

PropertyEditorRegistry::registerEditor(ITypeDefinitions::INTEGER, new GenericPropertyEditor(FILTER_VALIDATE_INT));
PropertyEditorRegistry::registerEditor(ITypeDefinitions::INT, new GenericPropertyEditor(FILTER_VALIDATE_INT));
PropertyEditorRegistry::registerEditor(ITypeDefinitions::DOUBLE, new GenericPropertyEditor(FILTER_VALIDATE_FLOAT));
PropertyEditorRegistry::registerEditor(ITypeDefinitions::FLOAT, new GenericPropertyEditor(FILTER_VALIDATE_FLOAT));
PropertyEditorRegistry::registerEditor(ITypeDefinitions::BOOLEAN, new BooleanPropertyEditor());
PropertyEditorRegistry::registerEditor(ITypeDefinitions::URL, new GenericPropertyEditor(FILTER_VALIDATE_URL));
PropertyEditorRegistry::registerEditor(ITypeDefinitions::EMAIL, new GenericPropertyEditor(FILTER_VALIDATE_EMAIL));
PropertyEditorRegistry::registerEditor(ITypeDefinitions::IP, new GenericPropertyEditor(FILTER_VALIDATE_IP));
PropertyEditorRegistry::registerEditor(ITypeDefinitions::IPv4, new GenericPropertyEditor(FILTER_VALIDATE_IP, FILTER_FLAG_IPV4));
PropertyEditorRegistry::registerEditor(ITypeDefinitions::IPv6, new GenericPropertyEditor(FILTER_VALIDATE_IP, FILTER_FLAG_IPV6));
PropertyEditorRegistry::registerEditor(ITypeDefinitions::UNIX_TIMESTAMP, new UnixTimestampPropertyEditor());
PropertyEditorRegistry::registerEditor(ITypeDefinitions::STRING, new StringPropertyEditor());
PropertyEditorRegistry::registerEditor(ITypeDefinitions::BASE64, new Base64StringPropertyEditor());

PropertyEditorRegistry::registerEditor(ConstantPropertyEditor::TYPE_STRING, new ConstantPropertyEditor());
