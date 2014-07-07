<?php
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
use Bee\Utils\ITypeDefinitions;

/**
 * Class Bee_Framework
 */
class Bee_Framework {

    const WEAVING_PACKAGE_PREFIX = 'Bee_';

	const CLASS_FILE_CACHE_PREFIX = '__BeeClassFileCache_';

	const GENERATED_CLASS_CODE_MARKER = '__CLASS_IS_GENERATED';
	
	private static $beeHiveLocation;

	private static $applicationId = false;

	/**
	 * @var Bee_Weaving_IEnhancedClassesStore
	 */
    private static $enhancedClassesStore = null;

    /**
     * todo: quick n dirty
     * @var array
     */
    private static $weavingExcludedClasses = array();

    private static $weaveDuringClassloading = false;

	private static $missedClassNames = array();

	private static $classFileMap;

	private static $productionMode = false;

	/**
	 * @param Bee_Weaving_IEnhancedClassesStore $enhancedClassesStore
	 */
	public static function setEnhancedClassesStore(Bee_Weaving_IEnhancedClassesStore $enhancedClassesStore) {
		self::$enhancedClassesStore = $enhancedClassesStore;
	}

	/**
	 * @return Bee_Weaving_IEnhancedClassesStore
	 */
	public static function getEnhancedClassesStore() {
		return self::$enhancedClassesStore;
	}

    public static function excludeFromWeaving($className) {
        self::$weavingExcludedClasses[$className] = true;
    }
	
	public static function setApplicationId($applicationId) {
		self::$applicationId = $applicationId;
	}
	
	public static function getApplicationId() {
		return self::$applicationId;
	}

	/**
	 * Main bootstrap method for the framework. Basically just initializes the framework classloader.
	 *
	 * @return void
	 */
	static function init() {
		self::$beeHiveLocation = dirname(dirname(__FILE__));
		self::addApplicationIncludePath(self::$beeHiveLocation);

		require_once dirname(__FILE__) . '/Cache/Manager.php';

//		spl_autoload_register(array(__CLASS__, 'autoload'));

		register_shutdown_function(array(__CLASS__, 'shutdown'));
//		Bee_Cache_Manager::init();
	}

	public static function handleError($errno, $errstr, $errfile, $errline) {
		error_log("HANDLING ERROR $errno");
		if((E_ERROR | E_RECOVERABLE_ERROR) & $errno) {
			
			error_log($errno .' : ' . $errstr);
			throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
		} else if (E_ALL & ~E_NOTICE & ~E_WARNING & $errno) {
			error_log($errno .' : ' . $errstr);
		}
	}
	
	/**
	 * Register application-specific include path with the framework classloader.
	 *
	 * @param String $includePath
	 * @return void
	 */
	public static function addApplicationIncludePath($includePath) {
        $incPath = get_include_path();
		set_include_path($includePath . PATH_SEPARATOR . $incPath);
    }

    
    /**
     * Main SPL autoloader function for the framework 
     *
     * @param string $className
     * @return boolean
	 * @deprecated
     */
    public static function autoload($className) {

    	if (class_exists($className, false) || interface_exists($className, false)) {
    		return false;
        }

		if(self::$productionMode) {
			if(!is_array(self::$classFileMap)) {
                try {
                    self::$classFileMap = Bee_Cache_Manager::retrieve(self::CLASS_FILE_CACHE_PREFIX);
                } catch (Exception $e) {
                }
				if(!is_array(self::$classFileMap)) {
					self::$classFileMap = array();
				}
			}

			if(array_key_exists($className, self::$classFileMap)) {
				$cachedPath = self::$classFileMap[$className];
				if($cachedPath === self::GENERATED_CLASS_CODE_MARKER) {
					return false;
				}

				include $cachedPath;
				if (class_exists($className, false) || interface_exists($className, false)) {
					return false;
				}
			}

            if (class_exists($className)) {
			    array_push(self::$missedClassNames, $className);
            }
            // TODO: What to do if class-loading fails??
		}

        if(self::$enhancedClassesStore != null && !array_key_exists($className, self::$weavingExcludedClasses) && substr($className, 0, strlen(self::WEAVING_PACKAGE_PREFIX)) != self::WEAVING_PACKAGE_PREFIX) {
            // possibly a woven class

			if(self::$enhancedClassesStore->loadClass($className)) {
				return true;
			}

			if(self::$weaveDuringClassloading) {
				require_once dirname(__FILE__) . 'Bee/Weaving/Enhancer.php';

				$enhancer = new Bee_Weaving_Enhancer($className);
				if($enhancer->createEnhancedClass() !== false) {
					return true;
				}
			}
        }

        foreach(self::getClassFileLocations($className) as $loc) {
            include_once $loc;
            if (class_exists($className, false) || interface_exists($className, false)) {
                return true;
            }
        }

        return false;
    }

    public static function getClassFileLocations($className) {
        return array(
            str_replace('_', DIRECTORY_SEPARATOR, str_replace('\\', DIRECTORY_SEPARATOR, $className)) . '.php',
            $className . '.php'
        );
    }

	public static function shutdown() {
		if(self::$productionMode) {
			foreach(self::$missedClassNames as $missedClassName) {
				$ref = new ReflectionClass($missedClassName);
				self::$classFileMap[$missedClassName] = file_exists($ref->getFileName()) ? $ref->getFileName() : self::GENERATED_CLASS_CODE_MARKER;
			}
			Bee_Cache_Manager::store(self::CLASS_FILE_CACHE_PREFIX, self::$classFileMap);
		}

		Bee_Cache_Manager::shutdown();
	}
    
    /**
     * Convenience method, dispatches current request using a dispatcher context configured from the
     * given set of XML config locations.
     *
     * @param String $configLocation comma-separated string XML config files to load the bean definitions from
     * @return void
     */
    public static function dispatchRequestUsingXmlContext($configLocation) {
		try {
    		Bee_Utils_Assert::notNull($configLocation);
			$ctx = new Bee_Context_Xml($configLocation);
            self::dispatchRequestUsingContext($ctx);
		} catch (Exception $e) {
			self::handleException($e);
		}
    }

	/**
	 * Convenience method, dispatches current request using the given dispatcher context.
	 *
	 * @param Bee_IContext $ctx
	 * @return void
	 */
    public static function dispatchRequestUsingContext(Bee_IContext $ctx) {
		try {
    		Bee_Utils_Assert::notNull($ctx);
			$dp = new Bee_MVC_Dispatcher($ctx);
			$dp->dispatch();
		} catch (Exception $e) {
			self::handleException($e);
		}
    }

    public static function handleException(Exception $e) {
		$topLevelMessage = $e->getMessage();

        $js = '<script>'."\n";
            $js .= 'function toggle(event) {'."\n";

            $js .= 'console.dir(event)'."\n";
            $js .= 'event.cancelBubble = true;'."\n";
            $js .= 'event.stopImmediatePropagation();'."\n";
            $js .= 'var ele = event.target.nextElementSibling;'."\n";
                $js .= 'if (ele.style.display == "none") {'."\n";
                        $js .= 'ele.style.display = "block";'."\n";
                $js .= '} else {'."\n";
                    $js .= 'ele.style.display = "none";'."\n";
                $js .= '}'."\n";
            $js .= '}'."\n";
        $js .= '</script>'."\n";

        echo $js;


        $excCnt = 0;

		while (!is_null($e)) {
            $excCnt += 1;
            echo '<div style="padding: 0 0 2px 0; margin: 0 2px 10px 2px; border: solid 1px #666;">';
                echo '<div style="background-color: #666; color: #fff; margin-bottom: 5px; padding: 5px; cursor: pointer;" onclick="javascript:toggle(event);">'.$excCnt.'. Exception: "'.get_class($e).'"</div>';

                echo '<div>';
                    echo '<div style="padding: 0 0 2px 0; margin: 10px; border: solid 1px #aaa; color: #aaa;">';
                        echo '<div style="background-color: #aaa; color: #666; padding: 5px; cursor: pointer;" onclick="toggle(event);">Message</div>';
                        echo '<div style="padding: 5px;">';
                            echo $e->getMessage();
                        echo '</div>';
                    echo '</div>';

                    echo '<div style="padding: 0 0 2px 0; margin: 10px; border: solid 1px #aaa; color: #aaa;">';
                        echo '<div style="background-color: #aaa; color: #666; padding: 5px; cursor: pointer;" onclick="toggle(event);">Stracktrace</div>';
                        echo '<div style="padding: 5px; font-size: 10px; display: none;">';
                            self::printArray($e->getTrace());
                        echo '</div>';
                    echo '</div>';
                echo '</div>';
            echo '</div>';


//            echo 'Root-Cause: &nbsp;';
            if ($e instanceof Bee_Exceptions_Base) {
                $e = $e->getCause();
            } else {
                $e = null;
            }

		}
		
		error_log($topLevelMessage, E_USER_WARNING);
    }
    
    private static function printSpaces($count) {
    	for ($i=0; $i<$count; $i++) {
    		echo '&nbsp;';
    	}
    }
    
    public static function printArray($output, $level=0) {
    	if (is_array($output)) {
    		foreach ($output as $key => $value) {
    			self::printSpaces($level*4);
    			echo $key;
    			if (is_array($value)) {
    				echo '<br/>';
    			}
    			self::printArray($value, $level+1);
    		}
    	} else {
    		self::printSpaces($level*4);
    		if (is_object($output)) {
    			echo 'Object - Class: ';
    			echo get_class($output);
    		} else {
    			echo $output;
    		}
    		echo '<br/>';
    	}
		if ($level==1) {
			echo '<hr/>';
		}
    }
    
    
	/**
	 * The applicationName is an identifier that can be used
	 * to uniquely identify the application - e.g. in the
	 * SessionContext - even from a different entry page.
	 * 
	 * @return String
	 */
	public static final function getApplicationName() {
		return self::$applicationName;
	}

	public static function setProductionMode($productionMode) {
		self::$productionMode = $productionMode;
	}

	public static function getProductionMode() {
		return self::$productionMode;
	}

	/**
	 * @param string $className
	 * @return Logger
	 */
	public static function getLoggerForClass($className) {
		return Logger::getLogger(str_replace('_', '.', str_replace('\\', '.', $className)));
	}
}

Bee_Framework::init();

//require_once dirname(__FILE__) . '/Utils/ITypeDefinitions.php';

interface TYPES extends ITypeDefinitions {
}