<?php
namespace Bee\MVC;
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

/**
 * Interface IHttpStatusCodes - maps HTTP/1.1 status codes to symbolic names. Descriptions are taken from Wikipedia.
 *
 * @link http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
 * @package Bee\MVC
 */
interface IHttpStatusCodes {

	/**
	 * This means that the server has received the request headers, and that the client should proceed to send the
	 * request body (in the case of a request for which a body needs to be sent; for example, a POST request). If the
	 * request body is large, sending it to a server when a request has already been rejected based upon inappropriate
	 * headers is inefficient. To have a server check if the request could be accepted based on the request's headers
	 * alone, a client must send Expect: 100-continue as a header in its initial request and check if a 100 Continue
	 * status code is received in response before continuing (or receive 417 Expectation Failed and not continue).
	 */
	const HTTP_CONTINUE = 100;

	/**
	 * This means the requester has asked the server to switch protocols and the server is acknowledging that it will
	 * do so.
	 */
	const HTTP_SWITCHING_PROTOCOLS = 101;

	/**
	 * As a WebDAV request may contain many sub-requests involving file operations, it may take a long time to complete
	 * the request. This code indicates that the server has received and is processing the request, but no response is
	 * available yet. This prevents the client from timing out and assuming the request was lost.
	 */
	const HTTP_PROCESSING = 102;

	/**
	 * Standard response for successful HTTP requests. The actual response will depend on the request method used. In a
	 * GET request, the response will contain an entity corresponding to the requested resource. In a POST request the
	 * response will contain an entity describing or containing the result of the action.
	 */
	const HTTP_OK = 200;

	/**
	 * The request has been fulfilled and resulted in a new resource being created.
	 */
	const HTTP_CREATED = 201;

	/**
	 * The request has been accepted for processing, but the processing has not been completed. The request might or
	 * might not eventually be acted upon, as it might be disallowed when processing actually takes place.
	 */
	const HTTP_ACCEPTED = 202;

	/**
	 * The server successfully processed the request, but is returning information that may be from another source.
	 */
	const HTTP_NON_AUTHORITATIVE_INFORMATION = 203;

	/**
	 * The server successfully processed the request, but is not returning any content. Usually used as a response to a
	 * successful delete request.
	 */
	const HTTP_NO_CONTENT = 204;

	/**
	 * The server successfully processed the request, but is not returning any content. Unlike a 204 response, this
	 * response requires that the requester reset the document view.
	 */
	const HTTP_RESET_CONTENT = 205;

	/**
	 * The server is delivering only part of the resource due to a range header sent by the client. The range header is
	 * used by tools like wget to enable resuming of interrupted downloads, or split a download into multiple
	 * simultaneous streams.
	 */
	const HTTP_PARTIAL_CONTENT = 206;

	/**
	 * The message body that follows is an XML message and can contain a number of separate response codes, depending
	 * on how many sub-requests were made.
	 */
	const HTTP_MULTI_STATUS = 207;

	/**
	 * The members of a DAV binding have already been enumerated in a previous reply to this request, and are not being
	 * included again.
	 */
	const HTTP_ALREADY_REPORTED = 208;

	/**
	 * The server has fulfilled a GET request for the resource, and the response is a representation of the result of
	 * one or more instance-manipulations applied to the current instance.
	 */
	const HTTP_IM_USED = 226;


	/**
	 * Indicates multiple options for the resource that the client may follow. It, for instance, could be used to
	 * present different format options for video, list files with different extensions, or word sense disambiguation.
	 */
	const HTTP_REDIRECT_MULTIPLE_CHOICES = 300;

	/**
	 * This and all future requests should be directed to the given URI.
	 */
	const HTTP_REDIRECT_MOVED_PERMANENTLY = 301;

	/**
	 * The HTTP/1.0 specification (RFC 1945) required the client to perform a temporary redirect (the original
	 * describing phrase was "Moved Temporarily"),[5] but popular browsers implemented 302 with the functionality of a
	 * 303 See Other.
	 */
	const HTTP_REDIRECT_FOUND = 302;

	/**
	 * The response to the request can be found under another URI using a GET method. When received in response to a
	 * POST (or PUT/DELETE), it should be assumed that the server has received the data and the redirect should be
	 * issued with a separate GET message.
	 */
	const HTTP_REDIRECT_SEE_OTHER = 303;

	/**
	 * Indicates that the resource has not been modified since the version specified by the request headers
	 * If-Modified-Since or If-Match. This means that there is no need to retransmit the resource, since the client
	 * still has a previously-downloaded copy.
	 */
	const HTTP_REDIRECT_NOT_MODIFIED = 304;

	/**
	 * The requested resource is only available through a proxy, whose address is provided in the response. Many HTTP
	 * clients (such as Mozilla and Internet Explorer) do not correctly handle responses with this status code,
	 * primarily for security reasons.
	 */
	const HTTP_REDIRECT_USE_PROXY = 305;

	/**
	 * In this case, the request should be repeated with another URI; however, future requests should still use the
	 * original URI. In contrast to how 302 was historically implemented, the request method is not allowed to be
	 * changed when reissuing the original request. For instance, a POST request should be repeated using another POST
	 * request.
	 */
	const HTTP_REDIRECT_TEMPORARY_REDIRECT = 307;

	/**
	 * The request, and all future requests should be repeated using another URI. 307 and 308 (as proposed) parallel the
	 * behaviours of 302 and 301, but do not allow the HTTP method to change. So, for example, submitting a form to a
	 * permanently redirected resource may continue smoothly.
	 */
	const HTTP_REDIRECT_PERMANENT_REDIRECT = 308;

	/**
	 * The request cannot be fulfilled due to bad syntax
	 */
	const HTTP_BAD_REQUEST = 400;

	/**
	 * Similar to 403 Forbidden, but specifically for use when authentication is required and has failed or has not yet
	 * been provided. The response must include a WWW-Authenticate header field containing a challenge applicable to the
	 * requested resource.
	 */
	const HTTP_UNAUTHORIZED = 401;

	/**
	 * Reserved for future use. The original intention was that this code might be used as part of some form of digital
	 * cash or micropayment scheme, but that has not happened, and this code is not usually used.
	 */
	const HTTP_PAYMENT_REQUIRED = 402;

	/**
	 * The request was a valid request, but the server is refusing to respond to it. Unlike a 401 Unauthorized response,
	 * authenticating will make no difference.
	 */
	const HTTP_FORBIDDEN = 403;

	/**
	 * The requested resource could not be found but may be available again in the future. Subsequent requests by the
	 * client are permissible.
	 */
	const HTTP_NOT_FOUND = 404;

	/**
	 * A request was made of a resource using a request method not supported by that resource; for example, using GET on
	 * a form which requires data to be presented via POST, or using PUT on a read-only resource.
	 */
	const HTTP_METHOD_NOT_ALLOWED = 405;

	/**
	 * The requested resource is only capable of generating content not acceptable according to the Accept headers sent
	 * in the request.
	 */
	const HTTP_NOT_ACCEPTABLE = 406;

	/**
	 * The client must first authenticate itself with the proxy.
	 */
	const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;

	/**
	 * The server timed out waiting for the request. According to HTTP specifications: "The client did not produce a
	 * request within the time that the server was prepared to wait. The client MAY repeat the request without
	 * modifications at any later time."
	 */
	const HTTP_REQUEST_TIMEOUT = 408;

	/**
	 * Indicates that the request could not be processed because of conflict in the request, such as an edit conflict
	 * in the case of multiple updates.
	 */
	const HTTP_CONFLICT = 409;

	/**
	 * Indicates that the resource requested is no longer available and will not be available again. This should be
	 * used when a resource has been intentionally removed and the resource should be purged.
	 */
	const HTTP_GONE = 410;

	/**
	 * The request did not specify the length of its content, which is required by the requested resource.
	 */
	const HTTP_LENGTH_REQUIRED = 411;

	/**
	 * The server does not meet one of the preconditions that the requester put on the request.
	 */
	const HTTP_PRECONDITION_FAILED = 412;

	/**
	 * The request is larger than the server is willing or able to process.
	 */
	const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;

	/**
	 * The URI provided was too long for the server to process. Often the result of too much data being encoded as a
	 * query-string of a GET request, in which case it should be converted to a POST request.
	 */
	const HTTP_REQUEST_URI_TOO_LONG = 414;

	/**
	 * The request entity has a media type which the server or resource does not support. For example, the client
	 * uploads an image as image/svg+xml, but the server requires that images use a different format.
	 */
	const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;

	/**
	 * The client has asked for a portion of the file, but the server cannot supply that portion. For example, if the
	 * client asked for a part of the file that lies beyond the end of the file.
	 */
	const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;

	/**
	 * The server cannot meet the requirements of the Expect request-header field.
	 */
	const HTTP_EXPECTATION_FAILED = 417;

	/**
	 * Not a part of the HTTP standard, 419 Authentication Timeout denotes that previously valid authentication has
	 * expired. It is used as an alternative to 401 Unauthorized in order to differentiate from otherwise authenticated
	 * clients being denied access to specific server resources.
	 */
	const HTTP_AUTHENTICATION_TIMEOUT = 419;

	/**
	 * Not part of the HTTP standard, but defined by Spring in the HttpStatus class to be used when a method failed.
	 * This status code is deprecated by Spring.
	 */
	const HTTP_METHOD_FAILURE = 420;

	/**
	 * The request was well-formed but was unable to be followed due to semantic errors.
	 */
	const HTTP_UNPROCESSABLE_ENTITY = 422;

	/**
	 * The resource that is being accessed is locked.
	 */
	const HTTP_LOCKED = 423;

	/**
	 * The request failed due to failure of a previous request (e.g., a PROPPATCH).
	 */
	const HTTP_FAILED_DEPENDENCY = 424;

	/**
	 * The client should switch to a different protocol such as TLS/1.0.
	 */
	const HTTP_UPGRADE_REQUIRED = 426;

	/**
	 * The origin server requires the request to be conditional. Intended to prevent "the 'lost update' problem, where a
	 * client GETs a resource's state, modifies it, and PUTs it back to the server, when meanwhile a third party has
	 * modified the state on the server, leading to a conflict."
	 */
	const HTTP_PRECONDITION_REQUIRED = 428;

	/**
	 * The user has sent too many requests in a given amount of time. Intended for use with rate limiting schemes.
	 */
	const TOO_MANY_REQUESTS = 429;

	/**
	 * The server is unwilling to process the request because either an individual header field, or all the header
	 * fields collectively, are too large.
	 */
	const REQUEST_HEADER_FIELDS_TOO_LARGE = 431;

	/**
	 * Used in Nginx logs to indicate that the server has returned no information to the client and closed the
	 * connection (useful as a deterrent for malware).
	 */
	const NGINX_NO_RESPONSE = 444;

	/**
	 * A Microsoft extension. The request should be retried after performing the appropriate action.[14]
	 */
	const MICROSOFT_RETRY_WITH = 449;

	/**
	 * A Microsoft extension. This error is given when Windows Parental Controls are turned on and are blocking access
	 * to the given webpage.[15]
	 */
	const MICROSOFT_BLOCKED_BY_WINDOWS_PARENTAL_CONTROLS = 450;

	/**
	 * Defined in the internet draft "A New HTTP Status Code for Legally-restricted Resources".[16] Intended to be used
	 * when resource access is denied for legal reasons, e.g. censorship or government-mandated blocked access. A
	 * reference to the 1953 dystopian novel Fahrenheit 451, where books are outlawed.[17]
	 */
	const UNAVAILABLE_FOR_LEGAL_REASONS = 451;

	/**
	 * Used in Exchange ActiveSync if there either is a more efficient server to use or the server cannot access the
	 * users' mailbox. The client is supposed to re-run the HTTP Autodiscovery protocol to find a better suited server.
	 */
	const MICROSOFT_REDIRECT = 451;

	/**
	 * Nginx internal code similar to 431 but it was introduced earlier in version 0.9.4 (on January the 21th, 2011).
	 */
	const NGINX_REQUEST_HEADER_TOO_LARGE = 494;

	/**
	 * Nginx internal code used when SSL client certificate error occurred to distinguish it from 4XX in a log and an
	 * error page redirection.
	 */
	const NGINX_CERT_ERROR = 495;

	/**
	 * Nginx internal code used when client didn't provide certificate to distinguish it from 4XX in a log and an error
	 * page redirection.
	 */
	const NGINX_NO_CERT = 496;

	/**
	 * Nginx internal code used for the plain HTTP requests that are sent to HTTPS port to distinguish it from 4XX in a
	 * log and an error page redirection.
	 */
	const NGINX_HTTP_TO_HTTPS = 497;

	/**
	 * Used in Nginx logs to indicate when the connection has been closed by client while the server is still processing
	 * its request, making server unable to send a status code back.[22]
	 */
	const NGINX_CLIENT_CLOSED_REQUEST = 499;

	/**
	 * A generic error message, given when an unexpected condition was encountered and no more specific message is
	 * suitable.
	 */
	const INTERNAL_SERVER_ERROR = 500;

	/**
	 * The server either does not recognize the request method, or it lacks the ability to fulfill the request. Usually
	 * this implies future availability (e.g., a new feature of a web-service API).
	 */
	const NOT_IMPLEMENTED = 501;

	/**
	 * The server was acting as a gateway or proxy and received an invalid response from the upstream server.
	 */
	const BAD_GATEWAY = 502;

	/**
	 * The server is currently unavailable (because it is overloaded or down for maintenance). Generally, this is a
	 * temporary state.
	 */
	const SERVICE_UNAVAILABLE = 503;

	/**
	 * The server was acting as a gateway or proxy and did not receive a timely response from the upstream server.
	 */
	const GATEWAY_TIMEOUT = 504;

	/**
	 * The server does not support the HTTP protocol version used in the request.
	 */
	const HTTP_VERSION_NOT_SUPPORTED = 505;

	/**
	 * Transparent content negotiation for the request results in a circular reference.
	 */
	const VARIANT_ALSO_NEGOTIATES = 506;

	/**
	 * The server is unable to store the representation needed to complete the request.
	 */
	const INSUFFICIENT_STORAGE = 507;

	/**
	 * The server detected an infinite loop while processing the request (sent in lieu of 208 Already Reported).
	 */
	const LOOP_DETECTED = 508;

	/**
	 * This status code is not specified in any RFCs. Its use is unknown.
	 */
	const BANDWIDTH_LIMIT_EXCEEDED = 509;

	/**
	 * Further extensions to the request are required for the server to fulfill it.
	 */
	const NOT_EXTENDED = 510;

	/**
	 * The client needs to authenticate to gain network access. Intended for use by intercepting proxies used to control
	 * access to the network (e.g., "captive portals" used to require agreement to Terms of Service before granting full
	 * Internet access via a Wi-Fi hotspot).
	 */
	const NETWORK_AUTHENTICATION_REQUIRED = 511;
}