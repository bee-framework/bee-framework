<?php
namespace Bee\MVC;
/*
 * Copyright 2008-2015 the original author or authors.
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
use Bee\Context\BeanNotOfRequiredTypeException;
use Bee\Context\BeansException;
use Bee\Context\NoSuchBeanDefinitionException;
use Bee\IContext;
use Bee\MVC\Session\DispatcherAdapter;
use Bee\Utils\Assert;
use Bee\Utils\TLogged;
use Exception;

/**
 * The dispatcher is the main entry point into an Bee MVC application. It acts as a front controller, i.e. it handles incoming
 * requests by dispatching them to specific backen controllers.
 * <p/>
 * The dispatcher uses for its configuration a bean container (the 'context'), which is an instance of <code>Bee\IContext</code>.
 * All collaborators required by the dispatcher are looked up from this context. The two collaborators required in any case are:
 * <ul>
 * <li><b>handlerMapping</b>: an instance of <code>Bee\MVC\IHandlerMapping</code> that is used to determine the name of the back
 * controller bean for the curret request.</li>
 * <li><b>viewResolver</b>: an instance of <code>IViewResolver</code> that is used to map view names returned by the back
 * controllers to actual view implementations.</li>
 * </ul>
 * <p/>
 * Conceptually, this Dispatcher is based entirely on the implementation of the DispathcerServler in the
 * {@link http://www.springframework.org Spring Framework}.
 * For additional information on the concepts, please refer to the chapter on Web MVC in the Spring documentation.
 *
 * @see \Bee\IContext
 * @see \Bee\MVC\IHandlerMapping
 * @see IViewResolver
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 * @author Benjamin Hartmann
 */
class Dispatcher implements IFilterChain {
    use TLogged;

	const REQUEST_BUILDER_BEAN_NAME = 'requestBuilder';

	const HANDLER_MAPPING_BEAN_NAME = 'handlerMapping';

	const VIEW_RESOLVER_BEAN_NAME = 'viewResolver';

	const FILTER_CHAIN_PROXY_NAME = 'filterChainProxy';

	const HANDLER_EXCEPTION_RESOLVER_NAME = 'handlerExceptionResolver';

	/**
	 * The dispatcher responsible for the current request
	 *
	 * @var Dispatcher
	 */
	private static $currentDispatcher;

	/**
	 * @var IHttpRequest
	 */
	private static $currentRequest = null;

	/**
	 * The root context used by this dispatcher
	 *
	 * @var IContext
	 */
	private $context;

	/**
	 * @var \Bee\MVC\IRequestBuilder
	 */
	private $requestBuilder;

	/**
	 * The handler mapping used by this dispatcher
	 *
	 * @var \Bee\MVC\IHandlerMapping
	 */
	private $handlerMapping;


	/**
	 * The view resolver used by this dispatcher
	 *
	 * @var IViewResolver
	 */
	private $viewResolver;

	/**
	 * Enter description here...
	 *
	 * @var IFilter
	 */
	private $filterChainProxy;

	/**
	 *
	 * @var IHandlerExceptionResolver
	 */
	private $handlerExceptionResolver;

	/**
	 * Returns the current dispatcher (i.e. the one handling this request).
	 * Use this to gain access to e.g. the bean context.
	 *
	 * @return Dispatcher
	 */
	public static function get() {
		return self::$currentDispatcher;
	}

	/**
	 * @throws Exception
	 * @return IHttpRequest
	 */
	public static function getCurrentRequest() {
		if (is_null(self::$currentRequest)) {
			throw new Exception('No request object constructed yet');
		}

		return self::$currentRequest;
	}

	/**
	 * Allows to dispatch control to sub-controllers from within a current request. Intended to be used to include hierarchical structures
	 * which must be also available as first-class handlers (e.g. for AJAX-based updates).
	 *
	 * @param IHttpRequest $request
	 * @return void
	 */
	public static function includeDispatch(IHttpRequest $request) {
		Assert::notNull(self::$currentDispatcher, 'No current dispatcher set - create an instance of Bee\MVC\Dispatcher and use its \'dispatch()\' method instead of \'includeDispatch()\'');
		Assert::notNull($request, 'Request object must not be null');

		// @todo: maybe use the apache-only virtual() function if available?
		self::$currentDispatcher->dispatchInternally($request);
	}

	/**
	 *
	 * @throws NoSuchBeanDefinitionException
	 * @throws BeanNotOfRequiredTypeException
	 * @throws BeansException
	 *
	 * @param String $beanName
	 * @param String $requiredType
	 * @return Object
	 */
	public static function getBeanFromDispatcherContext($beanName, $requiredType = null) {
		return self::get()->getContext()->getBean($beanName, $requiredType);
	}

	/**
	 * Construct a new dispatcher based on the given context.
	 *
	 * @param IContext $context
	 */
	public function __construct(IContext $context) {
		$this->context = $context;
		$this->init();
	}

	/**
	 * Initializes this dispatcher.
	 *
	 * @return void
	 */
	protected function init() {
		if ($this->context->containsBean(DispatcherAdapter::SESSION_HANDLER_NAME)) {
			$this->getLog()->info('custom session handler configured, setting it as PHP session_set_save_handler()');
			$sessionAdapter = new DispatcherAdapter($this->context);
			session_set_save_handler(
				array(&$sessionAdapter, "open"),
				array(&$sessionAdapter, "close"),
				array(&$sessionAdapter, "read"),
				array(&$sessionAdapter, "write"),
				array(&$sessionAdapter, "destroy"),
				array(&$sessionAdapter, "gc")
			);
		}

		try {
			$this->requestBuilder = $this->context->getBean(self::REQUEST_BUILDER_BEAN_NAME, '\Bee\MVC\IRequestBuilder');
		} catch (NoSuchBeanDefinitionException $ex) {
			$this->getLog()->debug('no RequestBuilder configured, using DefaultRequestBuilder');
			$this->requestBuilder = new DefaultRequestBuilder();
		}

		self::$currentRequest = $this->requestBuilder->buildRequestObject();

		$this->handlerMapping = $this->context->getBean(self::HANDLER_MAPPING_BEAN_NAME, 'Bee\MVC\IHandlerMapping');
		$this->viewResolver = $this->context->getBean(self::VIEW_RESOLVER_BEAN_NAME, 'Bee\MVC\IViewResolver');

		try {
			$this->filterChainProxy = $this->context->getBean(self::FILTER_CHAIN_PROXY_NAME, 'Bee\MVC\IFilter');
		} catch (NoSuchBeanDefinitionException $ex) {
			$this->getLog()->debug('no filter chain proxy configured');
		}

		try {
			$this->handlerExceptionResolver = $this->context->getBean(self::HANDLER_EXCEPTION_RESOLVER_NAME, 'Bee\MVC\IHandlerExceptionResolver');
		} catch (NoSuchBeanDefinitionException $ex) {
			$this->getLog()->debug('no exception resolver configured');
		}
	}

	/**
	 *
	 * @return IContext
	 */
	protected function getContext() {
		return $this->context;
	}

	/**
	 * Main dispatch method. Entry point into the whole request lifecycle of Bee MVC.
	 *
	 * @return void
	 */
	public function dispatch() {
		self::$currentDispatcher = $this;
//		self::$currentRequest = $this->buildRequestObject();

		if (!is_null($this->filterChainProxy)) {
			$this->filterChainProxy->doFilter(self::$currentRequest, $this);
		} else {
			$this->doFilter(self::$currentRequest);
		}

//		Manager::shutdown();
	}

	/**
	 * @param IHttpRequest $request
	 * @throws Exception
	 */
	public function doFilter(IHttpRequest $request) {
		$this->dispatchInternally($request);
	}

	/**
	 * @param IHttpRequest $request
	 * @throws Exception
	 */
	private function dispatchInternally(IHttpRequest $request) {
		$handler = null;
		try {
			$mav = null;
			$interceptors = array();
			$handlerException = null;
			$interceptorIndex = -1;
			try {
				$mappedHandler = $this->handlerMapping->getHandler($request);
				$interceptors = $mappedHandler->getInterceptors();
				$handler = $mappedHandler->getHandler();

				$mav = null;
				for ($i = 0; $i < count($interceptors); $i++) {
					$interceptor = $interceptors[$i];
					$interceptorResult = $interceptor->preHandle($request, $handler);
					if (!$interceptorResult) {
						//				$this->triggerAfterCompletion($handler, $interceptorIndex, $request, null);
						return;
					}
					$interceptorIndex = $i;
					if($interceptorResult instanceof ModelAndView) {
						$mav = $interceptorResult;
						break;
					}
				}

				// @todo: introduce HandlerAdapter
				if(is_null($mav)) {
					$mav = $handler->handleRequest($request);
				}

			} catch (Exception $e) {
				$this->getLog()->info('handler or interceptor exception caught, trying to resolve appropriate error view', $e);
				// @todo: handle exceptions caused by handlers properly (i.e. as application level exceptions)
				if ($this->handlerExceptionResolver) {
					$mav = $this->handlerExceptionResolver->resolveException($request, $handler, $e);
				}

				if (!$mav) {
					throw $e;
				}

				// got a view, make sure the rest of the request processing runs as intended (esp. post-handling)
				$handlerException = $e;
			}

			if ($mav instanceof ModelAndView) {
				$mav->addModelValue(Model::CURRENT_REQUEST_KEY, $request);
				$this->viewResolver->resolveModelAndView($mav, $request);

				if(!is_null($handlerException) && !count($interceptors)) {
					// We were unable to resolve a handler and its interceptors due to an exception being thrown along
					// the way, but we have an error view. Assume the error view needs the interceptor post-handlers to
					// run normally: fetch list of configured interceptors from handler mapping directly.
					$interceptors = $this->handlerMapping->getInterceptors();
				}
				// Apply postHandle methods of registered interceptors.
				for ($i = $interceptorIndex; $i >= 0; $i--) {
					$interceptor = $interceptors[$i];
					$interceptor->postHandle($request, $handler, $mav);
				}

				$mav->renderModelInView();
			}
		} catch (Exception $e) {
			throw $e;
		}
	}
}
