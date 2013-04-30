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
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class Bee_Utils_Env {

    const PATH_INFO = 'PATH_INFO';
    const ORIG_PATH_INFO = 'ORIG_PATH_INFO';

    public static $USER_AGENTS = array('msie', 'firefox', 'safari', 'webkit', 'opera', 'netscape', 'konqueror', 'gecko');
	
	private static $pathInfo;
	private static $absoluteBasePath;
	private static $basePath;
	private static $dispatcher;
	private static $applicationPath;
	private static $php_self;
    /**
     * @var Bee_Utils_UserAgent
     */
    private static $userAgent;
	
	
	
	/**
	 * The basePath is the path to the root of the webserver.
	 * It is likely to be extended by a customer-id or user-
	 * name.
	 *
	 * @return String
	 */
	public static final function getAbsoluteBasePath() {
		if (is_null(self::$absoluteBasePath)) {
            self::$absoluteBasePath = self::getWebserverDocumentRoot().'/';
            $basePath = self::getBasePath();

            if (Bee_Utils_Strings::hasText(self::$absoluteBasePath) && Bee_Utils_Strings::hasText($basePath) && !preg_match('#'.DIRECTORY_SEPARATOR.'$#i', self::$absoluteBasePath) && !preg_match('#^'.DIRECTORY_SEPARATOR.'#i', $basePath)) {
                echo 'MUST ADD THE THING!!!!<br/>';
                self::$absoluteBasePath .= DIRECTORY_SEPARATOR;
            }
            self::$absoluteBasePath .= $basePath;
		}
		return self::$absoluteBasePath;
	}

	/**
	 * The basePath is the path to the root of the webserver.
	 * It is likely to be extended by a customer-id or user-
	 * name.
	 *
	 * @return String
	 */
	public static final function getBasePath() {
		if (is_null(self::$basePath)) {
			self::$basePath = dirname(self::getPhpSelf());
			if (self::$basePath[0]==DIRECTORY_SEPARATOR) {
				self::$basePath = substr(self::$basePath, 1);
			}
			if (self::$basePath==false) {
				self::$basePath = '';
			}
		}
		return self::$basePath;
	}


	
	/**
	 * The applicationIndex is the .php file that will be the
	 * entry page for the application
	 *
	 * @return String
	 */
	public static final function getDispatcher() {
		if (is_null(self::$dispatcher)) {
			self::$dispatcher = pathinfo(self::getPhpSelf(), PATHINFO_FILENAME).'.'.pathinfo(self::getPhpSelf(), PATHINFO_EXTENSION);
		}
		return self::$dispatcher;
	}

    public static final function getApplicationIndex() {
        return self::getDispatcher();
    }

	
	/**
	 * The applicationPath is the path where the customized
	 * application components and views lie.
	 *
	 * @return String
	 */
	public static final function getApplicationPath() {
		if (is_null(self::$applicationPath)) {
			self::$applicationPath = pathinfo(self::getPhpSelf(), PATHINFO_FILENAME);
		}
		return self::$applicationPath;
	}

	
	
	/**
	 * The webserverDocumentRoot is the path to the document
	 * root directory of the webserver.
	 *
	 * @return String
	 */
	public static final function getWebserverDocumentRoot() {
		return $_SERVER['DOCUMENT_ROOT'];
	}

	
	
	/**
	 * The URL to the server
	 *
	 * @return String
	 */
	public static final function getProtocol() {
		$protocol = $_SERVER['HTTP_REFERER'];
        $pos = strpos($protocol, '://');
        if ($pos===false) {
            return 'http';
        }
        return substr($protocol, 0, $pos);
	}

	/**
	 * The URL to the server
	 *
	 * @return String
	 */
	public static final function getHost() {
		$host = $_SERVER['HTTP_HOST'];
		try {
			Bee_Utils_Assert::hasText($host);
		} catch (Exception $e) {
			$host = $_SERVER['SERVER_NAME'];
			try {
				Bee_Utils_Assert::hasText($host);
			} catch (Exception $e) {
				$host = $_SERVER['SERVER_ADDR'];
			}
		}

		// strip port number from host name (this should probably only be necessary when HTTP_HOST was used)
		$colPos = strpos($host, ':');
		if($colPos !== false) {
			$host = substr($host, 0, $colPos);
		}

		return $host;
	}

	public static function getPhpSelf() {
		if (is_null(self::$php_self)) {
			self::$php_self = substr($_SERVER["PHP_SELF"], 0, stripos($_SERVER["PHP_SELF"], '.php')+4);
		}
		return self::$php_self;
	}
	
	
	/**
	 * Usage of this method is really discouraged, since 
	 *
	 * @return String
	 */
	public static function getHtmlBase() {
		$base  = 'http://';
		$base .= self::getHost();
		$base .= '/';
		try {
			Bee_Utils_Assert::hasText(self::getBasePath());
			$base .= self::getBasePath();
			$base .= '/';
		} catch (Exception $e) {
		}
		return $base;
	}
	
	
	/**
	 * Returns the PATH_INFO (i.e. any additional path trailing the actual PHP file)
	 *
	 * @return string
	 */
	public static function getPathInfo() {
		/**
		 * Original code:
		 * $pathInfo = $_SERVER['PATH_INFO'];
		 * 
		 * 
		 * On some servers "PATH_INFO" is set in $_SERVER['ORIG_PATH_INFO']. If there is no
		 * extra path set, $_SERVER['ORIG_PATH_INFO'] will contain the script name, which
		 * is also set in $_SERVER['ORIG_SCRIPT_NAME'] or $_SERVER['SCRIPT_NAME']. 
		 */

		if (is_null(self::$pathInfo)) {
            $pathInfo = array_key_exists(self::PATH_INFO, $_SERVER) ? $_SERVER[self::PATH_INFO] : null;
			if(Bee_Utils_Strings::hasText($pathInfo)) {
				self::$pathInfo = $pathInfo;
				
			} else {
                $pathInfo = array_key_exists(self::ORIG_PATH_INFO, $_SERVER) ? $_SERVER[self::ORIG_PATH_INFO] : null;
                if(Bee_Utils_Strings::hasText($pathInfo)) {
                    if ($pathInfo == $_SERVER['ORIG_SCRIPT_NAME']) {
                        return '';
                    }
                    if ($pathInfo == $_SERVER['SCRIPT_NAME']) {
                        return '';
                    }
                    self::$pathInfo = $pathInfo;
                }
            }
		}
		return self::$pathInfo;
	}
	
	public static function getRequestHeaders($uppercaseKeys = false) {
		if(function_exists('apache_request_headers')) {
			$headers = apache_request_headers();
		} else {
			$phpSupportedHeaders = array (
				'HTTP_ACCEPT' => 'Accept',
				'HTTP_ACCEPT_CHARSET' => 'Accept-Charset',
				'HTTP_ACCEPT_ENCODING' => 'Accept-Encoding',
				'HTTP_ACCEPT_LANGUAGE' => 'Accept-Language', 
				'HTTP_CONNECTION' => 'Connection', 
				'HTTP_HOST' => 'Host', 
				'HTTP_REFERER' => 'Referer', 
				'HTTP_USER_AGENT' => 'User-Agent' 
			);
			$headers = array();
			foreach($phpSupportedHeaders as $key => $val) {
				if(array_key_exists($key, $_SERVER)) {
					$headers[$val] = $_SERVER[$key];
				}
			}
		}
		if($uppercaseKeys) {
			$headers = array_change_key_case($headers, CASE_UPPER);
		}
		return $headers;
	}

    /**
     * @static
     * @return Bee_Utils_UserAgent
     */
    public static function getUserAgent() {
        if (!self::$userAgent) {
            // Clean up agent and build regex that matches phrases for known browsers
            // (e.g. "Firefox/2.0" or "MSIE 6.0" (This only matches the major and minor
            // version numbers.  E.g. "2.0.0.6" is parsed as simply "2.0"

            self::$userAgent = new Bee_Utils_UserAgent();

            $userAgents = self::$USER_AGENTS;
            $userAgents[] = 'version';
            $agent = '';

            $agent = strtolower($agent ? $agent : $_SERVER['HTTP_USER_AGENT']);
            $pattern = '#(?<browser>'.join('|', $userAgents).')[/ ]+(?<version>[0-9]+(?:\.[0-9]+)?)#';

            // Find all phrases (or return empty array if none found)
            if (!preg_match_all($pattern, $agent, $matches)) {
                self::$userAgent->setName('unknown');
                self::$userAgent->setVersion(0);
            } else {

            // Since some UAs have more than one phrase (e.g Firefox has a Gecko phrase,
            // Opera 7,8 have a MSIE phrase), use the last one found (the right-most one
            // in the UA).  That's usually the most correct.
            $i = count($matches['browser'])-1;

            self::$userAgent->setName($matches['browser'][$i]);

            $version = $matches['version'][$i];
            if (in_array('version', $matches['browser'])) {
                $version = $matches['version'][$i-1];
            }

            self::$userAgent->setVersion($version);
            }
        }
        return self::$userAgent;
    }
}
?>