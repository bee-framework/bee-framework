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
 * Base interface for HttpRequests, which wrap all relevant knowledge about the current request. Request parameters SHOULD be accessed
 * through this interface rather than through the standard PHP superglobals ($_GET, $_POST, $_COOKIE, $_REQUEST, $_SERVER). Use of
 * superglobals is STRONGLY DISCOURAGED as the framework might decide to massage request parameters or otherwise change the request.
 * 
 * @todo what about $_FILES? we should probably make them accessible through this interface as well 
 * 
 * @see Bee_MVC_Dispatcher
 * 
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
interface Bee_MVC_IHttpRequest {
	
	/**
	 * Returns any extra path information associated with the URL the client sent when it made this request.
	 * The extra path information follows the servlet path but precedes the query string. This method returns
	 * <code>null</code> if there was no extra path information.
	 *
	 * @return String
	 */
	public function getPathInfo();
		
	
	/**
	 * Returns the name of the HTTP method with which this request was made, for example, GET, POST, or PUT.
	 * 
	 * @return String
	 */
	public function getMethod();
	
	
    /**
   	 * Enter description here...
   	 *
   	 * @return bool
   	 */
   	public function hasParameter($name);

	/**
	 * Enter description here...
	 *
	 * @param String $name
	 * @return String
	 */
	public function getParameter($name);
	

	/**
	 * Enter description here...
	 *
	 * @return array
	 */
	public function getParameterNames();
	
	
	/**
	 * Returns the value of the specified request header as a <code>String</code>. If the request did not include
	 * a header of the specified name, this method returns <code>null</code>. You can use this method with any
	 * request header.
	 * <p/>
	 * <b>Implementation note:</b> Natively, PHP recognizes only a subset of all possible HTTP request headers. These
	 * headers are made available to scripts via the <code>$_SERVER</code> superglobal.
	 * If the <code>apache_request_headers()</code> function is available (i.e. if PHP is running as an Apache or
	 * NSAPI module), it is used (rather than the superglobal) to determine all headers sent by the client. However,
	 * on IIS we have to fall back to the <code>$_SERVER</code> superglobal, so be aware that - depending on your
	 * environment - not all headers sent by the client will actually be avialable to your application.
	 *
	 * @param String $name
	 * @return String
	 */
	public function getHeader($name);

	
	/**
	 * Returns an array of all the header names this request contains. If the request has no headers,
	 * this method returns an empty array.
	 *
	 * @return array
	 */
	public function getHeaderNames();
	
//	public function getParamArray();
	
	public function addParameters(array $params);
}