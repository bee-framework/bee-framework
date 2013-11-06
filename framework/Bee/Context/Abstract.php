<?php
use Bee\Beans\MethodInvocation;
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
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
abstract class Bee_Context_Abstract extends Bee_Context_Config_BasicBeanDefinitionRegistry implements Bee_IContext, Bee_Context_Config_IContextAware {

    /**
     * @var Bee_IContext[] 
     */
    private static $registeredContexts = array();

    /**
     * @var string
     */
    private $identifier;

	/**
	 * Enter description here...
	 *
	 * @var Bee_IContext
	 */
	private $parent;
	
	/**
	 * Enter description here...
	 *
	 * @var array
	 */
	private $beansInCreation = array();
	
	/**
	 * Enter description here...
	 *
	 * @var Bee_Context_Config_IScope[]
	 */
	private $scopes;
	
	/**
	 * Enter description here...
	 *
	 * @var array
	 */
	private $dependentBeanMap = array();
	
	/**
	 * Enter description here...
	 *
	 * @var array
	 */
	private $dependenciesForBeanMap = array();

    /**
     * @var array
     */
    private $factoryBeanObjectCache = array();

    /**
     * @static
     * @param string $identifier
     * @return Bee_IContext
     */
    public static function getRegisteredContext($identifier) {
        return self::$registeredContexts[$identifier];
    }

	/**
	 * Enter description here...
	 *
	 */
	public function __construct($identifier='', $callInitMethod=true) {
        $this->identifier = $identifier;
		if ($callInitMethod) {
			$this->init();
		}
        self::$registeredContexts[$identifier] = $this;
	}

    public function getIdentifier() {
        return $this->identifier;
    }

	protected function init() {
		$this->registerScopes();
		$this->loadBeanDefinitions();
	}

	/**
	 * This method is supposed to be protected, but due to php's inability to define
	 * anonymous classes.
	 *
	 * **** DON'T CALL THIS METHOD ****
	 *
	 * @param String $beanName
	 * @param Bee_Context_Config_IBeanDefinition $beanDefinition
	 * @throws Exception
	 * @return null|object
	 */
	public function _createBean($beanName, Bee_Context_Config_IBeanDefinition $beanDefinition) {
		$instance = null;
		$this->beforeBeanCreation($beanName);
		try {
			$instance = $this->createBean($beanName, $beanDefinition);
			$this->afterBeanCreation($beanName);
		} catch (Exception $e) {
			$this->afterBeanCreation($beanName);
			throw $e;
		}
		return $instance;
	}

	public function setBeeContext(Bee_IContext $context) {
		$this->setParent($context);
	}

	/**
	 *
	 * @param string $beanName
	 * @param Bee_Context_Config_IBeanDefinition $beanDefinition
	 * @throws Bee_Context_BeanCreationException
	 * @return object
	 */
	protected function createBean($beanName, Bee_Context_Config_IBeanDefinition $beanDefinition) {

		// @todo: code copied from java, but unused yet
		// Make sure bean class is actually resolved at this point.
//		resolveBeanClass(mbd, beanName);

		// Prepare method overrides.
//		try {
//			$beanDefinition.prepareMethodOverrides();
//		} catch (BeanDefinitionValidationException ex) {
//			throw new BeanDefinitionStoreException(mbd.getResourceDescription(),
//					beanName, "Validation of method overrides failed", ex);
//		}

		try {
			// Give BeanPostProcessors a chance to return a proxy instead of the target bean instance.
			$bean = $this->resolveBeforeInstantiation($beanName, $beanDefinition);
			if ($bean != null) {
				return $bean;
			}
		}
		catch (Exception $ex) {
            throw new Bee_Context_BeanCreationException($beanName, 'BeanPostProcessor before instantiation of bean failed - '.$ex->getMessage());
		}
		return $this->doCreateBean($beanName, $beanDefinition);
	}
	
    protected function resolveBeforeInstantiation($beanName, Bee_Context_Config_IBeanDefinition $beanDefinition) {
        $bean = null;
//        if (!Boolean.FALSE.equals(mbd.beforeInstantiationResolved)) {
            // Make sure bean class is actually resolved at this point.
            if (!$beanDefinition->isSynthetic() && $this->hasInstantiationAwareBeanPostProcessors()) {
                $bean = $this->applyBeanPostProcessorsBeforeInstantiation($beanDefinition->getBeanClassName(), $beanName);
                if ($bean != null) {
                    $bean = $this->applyBeanPostProcessorsAfterInitialization($bean, $beanName);
                }
            }
//            mbd.beforeInstantiationResolved = Boolean.valueOf(bean != null);
//        }
        return $bean;
    }


	/**
	 * Actually create the specified bean. Pre-creation processing has already happened
	 * at this point, e.g. checking <code>postProcessBeforeInstantiation</code> callbacks.
	 * <p>Differentiates between default bean instantiation, use of a
	 * factory method, and autowiring a constructor.
	 * @param string $beanName name of the bean
	 * @param Bee_Context_Config_IBeanDefinition $beanDefinition
	 * @throws Bee_Context_BeanCreationException
	 *
	 * @return Object a new instance of the bean
	 * @see #instantiateBean
	 * @see #instantiateUsingFactoryMethod
	 * @see #autowireConstructor
	 */
	protected function doCreateBean($beanName, Bee_Context_Config_IBeanDefinition $beanDefinition) {
		$beanInstance = $this->createBeanInstance($beanName, $beanDefinition);
		$instanceWrapper = new Bee_Beans_BeanWrapper($beanInstance);
		
		// Initialize the bean instance.
		try {
			
			$this->applyPropertyValues($beanName, $beanDefinition, $instanceWrapper, $beanDefinition->getPropertyValues());
			$this->invokeMethods($beanName, $beanInstance, $beanDefinition->getMethodInvocations());
			$exposedObject = $this->initializeBean($beanName, $beanInstance, $beanDefinition);
		} catch (Exception $ex) {
			if ($ex instanceof Bee_Context_BeanCreationException && $beanName === $ex->getBeanName()) {
				throw $ex;
			} else {
				throw new Bee_Context_BeanCreationException($beanName, 'Initialization of bean failed - '.$ex->getMessage());
			}
		}

		// Register bean as disposable.
		/* 		
		registerDisposableBeanIfNecessary(beanName, bean, mbd);
		*/

		return $exposedObject;
	}

	/**
	 * Initialize the given bean instance, applying factory callbacks
	 * as well as init methods and bean post processors.
	 * <p>Called from {@link #createBean} for traditionally defined beans,
	 * and from {@link #initializeBean} for existing bean instances.
	 * @param string $beanName the bean name in the factory (for debugging purposes)
	 * @param object $bean the new bean instance we may need to initialize
	 * @param Bee_Context_Config_IBeanDefinition $beanDefinition the bean definition that the bean was created with
	 * (can also be <code>null</code>, if given an existing bean instance)
	 * @throws Bee_Context_BeanCreationException
	 * @return object the initialized bean instance (potentially wrapped)
	 * @see Bee_Context_Config_IBeanNameAware
	 * @see \Bee\Context\Config\IScopeAware
	 * @see Bee_Context_Config_IContextAware
	 * @see #invokeInitMethods
	 */
	protected function initializeBean($beanName, $bean, Bee_Context_Config_IBeanDefinition $beanDefinition) {
		if ($bean instanceof Bee_Context_Config_IBeanNameAware) {
			$bean->setBeanName($beanName);
		}

		if ($bean instanceof \Bee\Context\Config\IScopeAware) {
			$beanDefinition->getScope($this);
		}

		if ($bean instanceof Bee_Context_Config_IContextAware) {
			$bean->setBeeContext($this);
		}

		$wrappedBean = $bean;
		if (is_null($beanDefinition) || !$beanDefinition->isSynthetic()) {
			$wrappedBean = $this->applyBeanPostProcessorsBeforeInitialization($wrappedBean, $beanName);
		}

		try {
			$this->invokeInitMethods($beanName, $wrappedBean, $beanDefinition);
		} catch (Exception $ex) {
			throw new Bee_Context_BeanCreationException($beanName, 'Invocation of init method failed - '.$ex->getMessage());
		}

		if (is_null($beanDefinition) || !$beanDefinition->isSynthetic()) {
			$wrappedBean = $this->applyBeanPostProcessorsAfterInitialization($wrappedBean, $beanName);
		}

		return $wrappedBean;
	}
	
    protected function applyBeanPostProcessorsBeforeInstantiation($beanClassName, $beanName) {
        foreach($this->getBeanPostProcessorNames() as $beanProcessorName) {
            if($beanName !== $beanProcessorName) {
                $beanProcessor = $this->getBean($beanProcessorName, 'Bee_Context_Config_IBeanPostProcessor');
                if ($beanProcessor instanceof Bee_Context_Config_IInstantiationAwareBeanPostProcessor) {
                    $result = $beanProcessor->postProcessBeforeInstantiation($beanClassName, $beanName);
                    if ($result != null) {
                        return $result;
                    }
                }
            }
        }
        return null;
    }

	public function applyBeanPostProcessorsBeforeInitialization($existingBean, $beanName) {
		$result = $existingBean;
        foreach($this->getBeanPostProcessorNames() as $beanProcessorName) {
            if($beanName !== $beanProcessorName) {
                $beanProcessor = $this->getBean($beanProcessorName, 'Bee_Context_Config_IBeanPostProcessor');
                $result = $beanProcessor->postProcessBeforeInitialization($result, $beanName);
            }
        }
		return $result;
	}

	public function applyBeanPostProcessorsAfterInitialization($existingBean, $beanName) {
		$result = $existingBean;
        foreach($this->getBeanPostProcessorNames() as $beanProcessorName) {
            if($beanName !== $beanProcessorName) {
                $beanProcessor = $this->getBean($beanProcessorName, 'Bee_Context_Config_IBeanPostProcessor');
                $result = $beanProcessor->postProcessAfterInitialization($result, $beanName);
            }
        }
		return $result;
	}
	
	/**
	 * Give a bean a chance to react now all its properties are set,
	 * and a chance to know about its owning context (this object).
	 * This means checking whether the bean implements InitializingBean or defines
	 * a custom init method, and invoking the necessary callback(s) if it does.
	 * @param string $beanName the bean name in the factory (for debugging purposes)
	 * @param object $bean the new bean instance we may need to initialize
	 * @param Bee_Context_Config_IBeanDefinition $beanDefinition the bean definition that the bean was created with
	 * (can also be <code>null</code>, if given an existing bean instance)
	 * @see #invokeCustomInitMethod
	 */
	protected function invokeInitMethods($beanName, $bean, Bee_Context_Config_IBeanDefinition $beanDefinition) {

		$isInitializingBean = ($bean instanceof Bee_Context_Config_IInitializingBean);
		if ($isInitializingBean) {
			$bean->afterPropertiesSet();
		}

		$initMethodName = (!is_null($beanDefinition) ? $beanDefinition->getInitMethodName() : null);
		if (!is_null($initMethodName) && !($isInitializingBean && 'afterPropertiesSet' === $initMethodName)) {
			$initMethod = array($bean, $initMethodName);
			if(is_callable($initMethod)) {
				call_user_func($initMethod);
			}
		}
	}
	
	
	/**
	 * Create a new instance for the specified bean, using an appropriate instantiation strategy:
	 * factory method or simple instantiation.
	 * @param String $beanName the name of the bean
	 * @param Bee_Context_Config_IBeanDefinition $beanDefinition the bean definition for the bean
	 * 
	 * @return Bee_Beans_BeanWrapper for the new instance
	 * @see #instantiateUsingFactoryMethod
	 * @see #instantiateBean
	 */
	protected function createBeanInstance($beanName, Bee_Context_Config_IBeanDefinition $beanDefinition) {

		if (!is_null($beanDefinition->getFactoryMethodName())) {
			return $this->instantiateUsingFactoryMethod($beanName, $beanDefinition);
		}
		
		return $this->instantiateBean($beanName, $beanDefinition);
	}


	/**
	 * Instantiate the bean using a named factory method. The method may be static, if the
	 * mbd parameter specifies a class, rather than a factoryBean, or an instance variable
	 * on a factory object itself configured using Dependency Injection.
	 *
	 * @param String $beanName the name of the bean
	 * @param Bee_Context_Config_IBeanDefinition $beanDefinition the bean definition for the bean
	 * @throws Bee_Context_BeanDefinitionStoreException
	 * @throws Bee_Context_BeanCreationException
	 * @return Bee_Beans_BeanWrapper for the new instance
	 * @see #getBean(String, Object[])
	 */
	protected function instantiateUsingFactoryMethod($beanName, Bee_Context_Config_IBeanDefinition $beanDefinition) {
		$factoryMethodName = $beanDefinition->getFactoryMethodName();
		$factoryBeanName = $beanDefinition->getFactoryBeanName();

		assert(!is_null($factoryMethodName));
		assert(!is_null($factoryBeanName) || !is_null($beanDefinition->getBeanClassName()));

		if ($factoryBeanName != null) {
			if ($factoryBeanName === $beanName) {
				throw new Bee_Context_BeanDefinitionStoreException('factory-bean reference points back to the same bean definition', $beanName);
			}
			$factoryBeanInstanceOrClassName = $this->getBean($factoryBeanName);
			if ($factoryBeanInstanceOrClassName == null) {
				// @todo: a lot of debug information is lost here
				throw new Bee_Context_BeanCreationException($factoryBeanName);
			}
		} else {
			// It's a static factory method on the bean class.
			$factoryBeanInstanceOrClassName = $beanDefinition->getBeanClassName();
		}
		
		$factory = array($factoryBeanInstanceOrClassName, $factoryMethodName); 
		if(!is_callable($factory)) {
			// @todo: a lot of debug information is lost here
			throw new Bee_Context_BeanCreationException($beanName);
		}

		return call_user_func_array($factory, $this->createArgsArray($beanName, $beanDefinition));
	}


	/**
	 * Enter description here...
	 *
	 * @param String $beanName
	 * @param Bee_Context_Config_IBeanDefinition $beanDefinition
	 * @return object
	 */
	protected function instantiateBean($beanName, Bee_Context_Config_IBeanDefinition $beanDefinition) {
		$beanClassName = $beanDefinition->getBeanClassName();
		$beanClass = new ReflectionClass($beanClassName);
		
		$args = $beanDefinition->getConstructorArgumentValues(); 
		if(is_null($args)||count($args) == 0) {
			return $beanClass->newInstance();
		}
		return $beanClass->newInstanceArgs($this->createArgsArray($beanName, $beanDefinition));
	}
	
	private function createArgsArray($beanName, \Bee\Context\Config\IMethodArguments $methodArguments) {
//		$typeConverter = null; // @todo: ???????????????????????????????????????????		
//		$valueResolver = new Bee_Context_BeanDefinitionValueResolver($this, $beanName, $beanDefinition, $typeConverter);
		$valueResolver = new Bee_Context_BeanDefinitionValueResolver($this, $beanName, $methodArguments);
		
		$args = array();
		foreach ($methodArguments->getConstructorArgumentValues() as $propValue) {
//			$value = $valueResolver->resolveValueIfNecessary('constructor/factory method argument', $propValue->getValue());
//			$args[] = $typeConverter->convertIfNecessary($value, $propValue->getTypeName());
			$args[] = $valueResolver->resolveValueIfNecessary('constructor/factory method argument', $propValue->getValue());
		}
		return $args;
	}

	/**
	 * Apply the given property values, resolving any runtime references
	 * to other beans in this context.
	 * @param String $beanName the bean name passed for better exception information
	 * @param Bee_Context_Config_IBeanDefinition $beanDefinition the bean definition
	 * @param Bee_Beans_BeanWrapper $beanWrapper the BeanWrapper wrapping the target object
	 * @param Bee_Beans_PropertyValue[] $propertyValues the new property values
	 * @throws Bee_Context_BeanCreationException
	 */
	protected function applyPropertyValues($beanName, Bee_Context_Config_IBeanDefinition $beanDefinition, Bee_Beans_BeanWrapper $beanWrapper, array $propertyValues = null) {
		if (is_null($propertyValues) || count($propertyValues) === 0) {
			return;
		}

		$valueResolver = new Bee_Context_BeanDefinitionValueResolver($this, $beanName, $beanDefinition);
		
		$deepCopy = array();
		foreach ($propertyValues as $propValue) {
			$propName = $propValue->getName(); 
			$deepCopy[$propName] = $valueResolver->resolveValueIfNecessary($propName, $propValue->getValue());
		}
		
		// Set our (possibly massaged) deep copy.
		try {
			$beanWrapper->setPropertyValues($deepCopy);
		} catch (Bee_Context_BeansException $ex) {
			throw new Bee_Context_BeanCreationException($beanName);
		}
	}

	/**
	 * @param $beanName
	 * @param $beanInstance
	 * @param MethodInvocation[] $methodInvocations
	 * @throws Bee_Context_InvalidPropertyException
	 */
	protected function invokeMethods($beanName, $beanInstance, array $methodInvocations = array()) {
		foreach($methodInvocations as $methodInvocation) {
			$method = array($beanInstance, $methodInvocation->getMethodName());
			if(!is_callable($method)) {
				 throw new Bee_Context_InvalidPropertyException($methodInvocation->getMethodName(), Bee_Utils_Types::getType($beanInstance), 'no such method found: '.$methodInvocation->getMethodName());
			}
			// todo: validate method signature??
			call_user_func_array($method, $this->createArgsArray($beanName, $methodInvocation));
		}
	}

	abstract protected function loadBeanDefinitions();
	
	/**
	 * Enter description here...
	 *
	 * @return void
	 */
	protected function registerScopes() {
		$uniqueId = $this->getIdentifier();
		$scopes = array(
			Bee_Context_Config_IBeanDefinition::SCOPE_CACHE => new Bee_Context_Config_Scope_Cache($uniqueId),
			Bee_Context_Config_IBeanDefinition::SCOPE_PROTOTYPE => new Bee_Context_Config_Scope_Prototype($uniqueId),
			Bee_Context_Config_IBeanDefinition::SCOPE_REQUEST => new Bee_Context_Config_Scope_Request($uniqueId),
			Bee_Context_Config_IBeanDefinition::SCOPE_SESSION => new Bee_Context_Config_Scope_Session($uniqueId)
		);
		$this->scopes = $scopes;
	}

	/**
	 * Callback before bean creation.
	 * <p>The default implementation register the bean as currently in creation.
	 * 
	 * @param String $beanName the name of the bean about to be created
	 * @return void
	 * @see #isPrototypeCurrentlyInCreation
 	 */
	protected function beforeBeanCreation($beanName) {
		$this->beansInCreation[$beanName] = TRUE;
	}
	

	
	/**
	 * Callback after bean creation.
	 * <p>The default implementation marks the bean as not in creation anymore.
	 * 
	 * @param String $beanName the name of the bean that has been created
	 * @return void
	 * @see #isPrototypeCurrentlyInCreation
	 */
	protected function afterBeanCreation($beanName) {
		unset($this->beansInCreation[$beanName]);
	}


	/**
	 * Return whether the specified bean is currently in creation.
	 *
	 * @param String $beanName the name of the bean
	 * @return bool
	 */
	public function isBeanCurrentlyInCreation($beanName) {
		return array_key_exists($beanName, $this->beansInCreation);
	}

	/**
	 * Register a dependent bean for the given bean,
	 * to be destroyed before the given bean is destroyed.
	 * @param String $beanName the name of the bean
	 * @param String $dependentBeanName the name of the dependent bean
	 */
	public function registerDependentBean($beanName, $dependentBeanName) {

        if(!array_key_exists($beanName, $this->dependentBeanMap)) {
			$this->dependentBeanMap[$beanName] = array();
		}		
		$this->dependentBeanMap[$beanName][$dependentBeanName]= TRUE;

		if(!array_key_exists($dependentBeanName, $this->dependenciesForBeanMap)) {
			$this->dependenciesForBeanMap[$dependentBeanName] = array();
		}		
		$this->dependenciesForBeanMap[$dependentBeanName][$beanName] = TRUE;
		
	}

	/**
	 * Determine whether a dependent bean has been registered under the given name.
	 * @param string $beanName the name of the bean
	 * @return boolean
	 */
	protected function hasDependentBean($beanName) {
		return array_key_exists($beanName, $this->dependentBeanMap);
	}

	
	
	/**
	 * Return the names of all beans which depend on the specified bean, if any.
	 * @param String $beanName the name of the bean
	 * @return array the array of dependent bean names, or an empty array if none
	 */
	public function getDependentBeans($beanName) {
		if(!is_array($this->dependentBeanMap[$beanName])) {
			return array();
		}
		return array_keys($this->dependentBeanMap[$beanName]);
	}

	
	
	/**
	 * Return the names of all beans that the specified bean depends on, if any.
	 * @param String $beanName the name of the bean
	 * @return array the array of names of beans which the bean depends on,
	 * or an empty array if none
	 */
	public function getDependenciesForBean($beanName) {
		if(!is_array($this->dependenciesForBeanMap[$beanName])) {
			return array();
		}
		return array_keys($this->dependenciesForBeanMap[$beanName]);
	}

	

	// ***************************************************************************************************
	// IMPLEMENTATION OF Bee_IContext
	// ***************************************************************************************************
	
	public function containsBean($beanName) {
		if ($this->containsBeanDefinition($beanName)) {
			return true;
		}
		return (!is_null($this->getParent()) && $this->getParent()->containsBean($beanName));
	}
	


	public function getBean($name, $requiredType=null) {

		$beanName = $this->transformedBeanName($name);
		
		// @todo: prototypes currently in creation: 
		
		if($this->isPrototypeCurrentlyInCreation($beanName)) {
			throw new Bee_Context_BeanCurrentlyInCreationException($beanName);
		}
		
		if(!is_null($this->getParent()) && !$this->containsBeanDefinition($beanName)) {
			return $this->getParent()->getBean($beanName, $requiredType);
		}
		
		$localBeanDefinition = $this->getBeanDefinition($beanName);
		
		// OK, we have a bean definition. how do we create / retrieve an instance of it? 
		// TODO: FactoryBean etc.
        $dependsOn = $localBeanDefinition->getDependsOn();

        if(!is_null($dependsOn)) {
            foreach ($dependsOn as $dep) {
                $this->getBean($dep);
                $this->registerDependentBean($dep, $beanName);
            }
        }

		$scopeName = $localBeanDefinition->getScope();
		$scope = $this->scopes[$scopeName];
		if(is_null($scope)) {
			throw new Exception("No scope registered for scope $scopeName"); 
		}
		
		// @todo: catch IllegalStateException in case scope is not active (e.g. no session started...)
		// not needed for session, request, prototype scopes but maybe for fancy new scope implementations...
		$scopedInstance = $scope->get($beanName, new Bee_Context_Abstract_ObjectFactoryImpl($beanName, $localBeanDefinition, $this));

        $bean = $this->getObjectForBeanInstance($scopedInstance, $name, $beanName, $localBeanDefinition);

		if (!is_null($requiredType) && !($bean instanceof $requiredType)) {
			throw new Bee_Context_BeanNotOfRequiredTypeException($beanName, $requiredType, get_class($bean));
		}
		
		return $bean;
		
	}

	/**
	 * Get the object for the given bean instance, either the bean
	 * instance itself or its created object in case of a FactoryBean.
	 * @param mixed $beanInstance the shared bean instance
	 * @param string $name name that may include factory dereference prefix
	 * @param string $beanName the canonical bean name
	 * @param Bee_Context_Config_IBeanDefinition $mbd the merged bean definition
	 * @throws Bee_Context_BeanIsNotAFactoryException
	 * @return object the object to expose for the bean
	 */
    protected function getObjectForBeanInstance(
            $beanInstance, $name, $beanName, Bee_Context_Config_IBeanDefinition $mbd) {

        // Don't let calling code try to dereference the factory if the bean isn't a factory.
        if (Bee_Context_Support_ContextUtils::isFactoryDereference($name) && !($beanInstance instanceof Bee_Context_IFactoryBean)) {
            throw new Bee_Context_BeanIsNotAFactoryException($name, get_class($beanInstance));
        }

        // Now we have the bean instance, which may be a normal bean or a FactoryBean.
        // If it's a FactoryBean, we use it to create a bean instance, unless the
        // caller actually wants a reference to the factory.
        if (!($beanInstance instanceof Bee_Context_IFactoryBean) || Bee_Context_Support_ContextUtils::isFactoryDereference($name)) {
            return $beanInstance;
        }

        $object = null;
        if ($mbd == null) {
            $object = $this->getCachedObjectForFactoryBean($beanName);
        }
        if ($object == null) {
            // Return bean instance from factory.
            // Caches object obtained from FactoryBean if it is a singleton.
            if ($mbd == null && $this->containsBeanDefinition($beanName)) {
                $mbd = $this->getBeanDefinition($beanName);
            }
            $synthetic = ($mbd != null && $mbd->isSynthetic());
            $object = $this->getObjectFromFactoryBean($beanInstance, $beanName, !$synthetic);
        }
        return $object;
    }

    /**
     * Obtain an object to expose from the given FactoryBean, if available
     * in cached form. Quick check for minimal synchronization.
     * @param string $beanName the name of the bean
     * @return mixed the object obtained from the FactoryBean,
     * or <code>null</code> if not available
     */
    protected function getCachedObjectForFactoryBean($beanName) {
        return $this->factoryBeanObjectCache[$beanName];
    }

    /**
     * Obtain an object to expose from the given FactoryBean.
     * @param Bee_Context_IFactoryBean $factory the FactoryBean instance
     * @param string $beanName the name of the bean
     * @param boolean $shouldPostProcess whether the bean is subject for post-processing
     * @return mixed the object obtained from the FactoryBean
     * @throws Bee_Context_BeanCreationException if FactoryBean object creation failed
     * @see org.springframework.beans.factory.FactoryBean#getObject()
     */
    protected function getObjectFromFactoryBean(Bee_Context_IFactoryBean $factory, $beanName, $shouldPostProcess) {
        if ($factory->isSingleton() /*&& $this->containsSingleton(beanName)*/) {
            if(array_key_exists($beanName, $this->factoryBeanObjectCache)) {
                $object = $this->factoryBeanObjectCache[$beanName];
            } else {
                $object = $this->doGetObjectFromFactoryBean($factory, $beanName, $shouldPostProcess);
                $this->factoryBeanObjectCache[$beanName] = $object;
            }
            return $object;
        } else {
            return $this->doGetObjectFromFactoryBean($factory, $beanName, $shouldPostProcess);
        }
    }

	/**
	 * Obtain an object to expose from the given FactoryBean.
	 * @param Bee_Context_IFactoryBean $factory the FactoryBean instance
	 * @param string $beanName the name of the bean
	 * @param boolean $shouldPostProcess whether the bean is subject for post-processing
	 * @throws Bee_Context_BeanCurrentlyInCreationException
	 * @throws Bee_Context_BeanCreationException
	 * @return mixed the object obtained from the FactoryBean
	 * @see org.springframework.beans.factory.FactoryBean#getObject()
	 */
    private function doGetObjectFromFactoryBean(Bee_Context_IFactoryBean $factory, $beanName, $shouldPostProcess) {
        try {
            $object = $factory->getObject();
        }
        catch (Bee_Context_FactoryBeanNotInitializedException $ex) {
            throw new Bee_Context_BeanCurrentlyInCreationException($beanName, $ex);
        }
        catch (Exception $ex) {
            throw new Bee_Context_BeanCreationException($beanName, "FactoryBean threw exception on object creation", $ex);
        }

        // Do not accept a null value for a FactoryBean that's not fully
        // initialized yet: Many FactoryBeans just return null then.
//        if ($object == null && isSingletonCurrentlyInCreation(beanName)) {
//            throw new Bee_Context_BeanCurrentlyInCreationException(
//                    beanName, "FactoryBean which is currently in creation returned null from getObject");
//        }

        if ($object != null && $shouldPostProcess) {
            try {
                $object = $this->applyBeanPostProcessorsAfterInitialization($object, $beanName);
            }
            catch (Exception $ex) {
                throw new Bee_Context_BeanCreationException($beanName, "Post-processing of the FactoryBean's object failed", $ex);
            }
        }

        return $object;
    }

	public function isTypeMatch($beanName, $type) {
		if(!is_null($this->getParent()) && !$this->containsBeanDefinition($beanName)) {
			return $this->getParent()->isTypeMatch($beanName, $type);
		}
		// @todo: TEST THIS!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		return is_subclass_of($this->getBeanDefinition($beanName)->getBeanClassName(), $type);
	}
	
	
	
	public function getType($beanName) {
		if(!is_null($this->getParent()) && !$this->containsBeanDefinition($beanName)) {
			return $this->getParent()->getType($beanName);
		}
		return $this->getBeanDefinition($beanName)->getBeanClassName();
	}
	
	public function getBeanNamesForType($className) {
        $allDefs = $this->getBeanDefinitions();
        $matches = array();
        foreach($allDefs as $name => $beanDefinition) {
            if(Bee_Utils_Types::isAssignable($beanDefinition->getBeanClassName(), $className)) {
                $matches[] = $name;
            }
        }
        return $matches;
    }
	
	/**
	 * Enter description here...
	 *
	 * @return Bee_IContext
	 */
	public function getParent() {
		return $this->parent;
	}
	
	
	/**
	 * Enter description here...
	 *
	 * @param String $beanName
	 * @return Boolean
	 */
	protected final function isPrototypeCurrentlyInCreation($beanName) {
		return array_key_exists($beanName, $this->beansInCreation);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param Bee_IContext $parent
	 * @return void
	 */
	public function setParent(Bee_IContext $parent) {
		$this->parent = $parent;
		if($parent instanceof Bee_Context_Config_IBeanDefinitionRegistry) {
			$this->setParentRegistry($parent);
		}
	}

    public function getModificationTimestamp() {
        return 0;
    }
}



/**
 * Workaround for an anonymous implementation of Bee_Context_Config_IObjectFactory.
 */
final class Bee_Context_Abstract_ObjectFactoryImpl implements Bee_Context_Config_IObjectFactory {

	/**
	 * Enter description here...
	 *
	 * @var string
	 */
	private $beanName;
	
	/**
	 * Enter description here...
	 *
	 * @var Bee_Context_Config_IBeanDefinition
	 */
	private $beanDefinition;
	
	/**
	 * Enter description here...
	 *
	 * @var Bee_Context_Abstract
	 */
	private $context;
	
	/**
	 * Enter description here...
	 *
	 * @param String $beanName
	 * @param Bee_Context_Config_IBeanDefinition $beanDefinition
	 * @param Bee_Context_Abstract $context
	 */
	public function __construct($beanName, Bee_Context_Config_IBeanDefinition $beanDefinition, Bee_Context_Abstract $context) {		
		$this->beanName = $beanName;
		$this->beanDefinition = $beanDefinition;
		$this->context = $context;
	}

	public function getObject() {
		return $this->context->_createBean($this->beanName, $this->beanDefinition);
	}

    function getModificationTimestamp() {
        return $this->context->getModificationTimestamp();
    }
}