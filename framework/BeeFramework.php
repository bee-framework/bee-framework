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

//require_once dirname(__FILE__).'/../libs/addendum/annotations.php';

class BeeFramework {
	
	private static $beeHiveLocation;

	private static $applicationId = false;
	
	public static function setApplicationId($applicationId) {
		self::$applicationId = $applicationId;
	}
	
	public static function getApplicationId() {
		return self::$applicationId;
	}
	
	//	private static $includePaths;
	
	/**
	 * Main bootstrap method for the framework. Basically just initializes the framework classloader.
	 *
	 * @return void
	 */
	static function init() {
		self::$beeHiveLocation = dirname(__FILE__);
		self::addApplicationIncludePath(self::$beeHiveLocation);
//		self::$includePaths = array(self::$beeHiveLocation);
//		set_error_handler(array('BeeFramework', 'handleError'));
		spl_autoload_register(array('BeeFramework', 'autoload'));
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
//		self::$includePaths[] = $includePath;
    }

    
    /**
     * Main SPL autoloader function for the framework 
     *
     * @param unknown_type $className
     * @return boolean
     */
    public static function autoload($className) {
    	if (class_exists($className, false) || interface_exists($className, false)) {
    		return false;
        }
        
        $class = $className . '.php';

        include_once $class;

        if (class_exists($className, false) || interface_exists($className, false)) {
            return true;
        }

		$class = str_replace('_', '/', $className) . '.php';
		
		include_once $class;

		if (class_exists($className, false) || interface_exists($className, false)) {
    		return true;
        }
        return false;
		
//        foreach(self::$includePaths as $includePath) {
//        	$class = $includePath . '/' . str_replace('_', '/', $className) . '.php';
//	        if (file_exists($class)) {
//	        	require_once $class;
//	            return true;
//	        }        	
//        }
//        return false;
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
			$dp = new Bee_MVC_Dispatcher($ctx);
			$dp->dispatch();    	
		} catch (Exception $e) {
			self::handleException($e);
		}
    }
    
    public static function handleException(Exception $e) {
		$topLevelMessage = $e->getMessage();

		while (!is_null($e)) {
			echo 'EXCEPTION ' . get_class($e) . '<br/>';
			echo 'Message: '.$e->getMessage().'<hr/>';
			self::printArray($e->getTrace());

			echo '<hr/>ROOT CAUSE : &nbsp;';
			if($e instanceof Bee_Exceptions_Base) {
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
}



BeeFramework::init();



interface TYPES extends Bee_Utils_ITypeDefinitions {
}
?>