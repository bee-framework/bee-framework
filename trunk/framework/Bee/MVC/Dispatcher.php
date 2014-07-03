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
use Bee\MVC\DefaultRequestBuilder;

/**
 * The dispatcher is the main entry point into an Bee MVC application. It acts as a front controller, i.e. it handles incoming
 * requests by dispatching them to specific backen controllers.
 * <p/>
 * The dispatcher uses for its configuration a bean container (the 'context'), which is an instance of <code>Bee_IContext</code>.
 * All collaborators required by the dispatcher are looked up from this context. The two collaborators required in any case are:
 * <ul>
 * <li><b>handlerMapping</b>: an instance of <code>Bee\MVC\IHandlerMapping</code> that is used to determine the name of the back
 * controller bean for the curret request.</li>
 * <li><b>viewResolver</b>: an instance of <code>Bee_MVC_IViewResolver</code> that is used to map view names returned by the back
 * controllers to actual view implementations.</li>
 * </ul>
 * <p/>
 * Conceptually, this Dispatcher is based entirely on the implementation of the DispathcerServler in the
 * {@link http://www.springframework.org Spring Framework}.
 * For additional information on the concepts, please refer to the chapter on Web MVC in the Spring documentation.
 *
 * @see Bee_IContext
 * @see Bee\MVC\IHandlerMapping
 * @see Bee_MVC_IViewResolver
 *
 * @author Michael Plomer <michael.plomer@iter8.de>
 * @author Benjamin Hartmann
 */
class Bee_MVC_Dispatcher implements Bee_MVC_IFilterChain {

	const REQUEST_BUILDER_BEAN_NAME = 'requestBuilder';

	const HANDLER_MAPPING_BEAN_NAME = 'handlerMapping';

	const VIEW_RESOLVER_BEAN_NAME = 'viewResolver';

	const FILTER_CHAIN_PROXY_NAME = 'filterChainProxy';

	const HANDLER_EXCEPTION_RESOLVER_NAME = 'handlerExceptionResolver';

	/**
	 * @var Logger
	 */
	protected $log;

	/**
	 * @return Logger
	 */
	protected function getLog() {
		if (!$this->log) {
			$this->log = Logger::getLogger(get_class($this));
		}
		return $this->log;
	}

	/**
	 * The dispatcher responsible for the current request
	 *
	 * @var Bee_MVC_Dispatcher
	 */
	private static $currentDispatcher;

	/**
	 * @var Bee_MVC_IHttpRequest
	 */
	private static $currentRequest = null;

	/**
	 * The root context used by this dispatcher
	 *
	 * @var Bee_IContext
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
	 * @var Bee_MVC_IViewResolver
	 */
	private $viewResolver;

	/**
	 * Enter description here...
	 *
	 * @var Bee_MVC_IFilter
	 */
	private $filterChainProxy;

	/**
	 *
	 * @var Bee_MVC_IHandlerExceptionResolver
	 */
	private $handlerExceptionResolver;

	/**
	 * Returns the current dispatcher (i.e. the one handling this request).
	 * Use this to gain access to e.g. the bean context.
	 *
	 * @return Bee_MVC_Dispatcher
	 */
	public static function get() {
		return self::$currentDispatcher;
	}

	/**
	 * @throws Bee_Exceptions_Base
	 * @return Bee_MVC_IHttpRequest
	 */
	public static function getCurrentRequest() {
		if (is_null(self::$currentRequest)) {
			throw new Bee_Exceptions_Base('No request object constructed yet');
		}

		return self::$currentRequest;
	}

	/**
	 * Allows to dispatch control to sub-controllers from within a current request. Intended to be used to include hierarchical structures
	 * which must be also available as first-class handlers (e.g. for AJAX-based updates).
	 *
	 * @param Bee_MVC_IHttpRequest $request
	 * @return void
	 */
	public static function includeDispatch(Bee_MVC_IHttpRequest $request) {
		Bee_Utils_Assert::notNull(self::$currentDispatcher, 'No current dispatcher set - create an instance of Bee_MVC_Dispatcher and use its \'dispatch()\' method instead of \'includeDispatch()\'');
		Bee_Utils_Assert::notNull($request, 'Request object must not be null');

		// @todo: maybe use the apache-only virtual() function if available?
		self::$currentDispatcher->dispatchInternally($request);
	}

	/**
	 *
	 * @throws Bee_Context_NoSuchBeanDefinitionException
	 * @throws Bee_Context_BeanNotOfRequiredTypeException
	 * @throws Bee_Context_BeansException
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
	 * @param Bee_IContext $context
	 */
	public function __construct(Bee_IContext $context) {
		$this->context = $context;
		$this->init();
	}


	/**
	 * Initializes this dispatcher.
	 *
	 * @return void
	 */
	protected function init() {
		if ($this->context->containsBean(Bee_MVC_Session_DispatcherAdapter::SESSION_HANDLER_NAME)) {
			$this->getLog()->info('custom session handler configured, setting it as PHP session_set_save_handler()');
			$sessionAdapter = new Bee_MVC_Session_DispatcherAdapter($this->context);
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
		} catch (Bee_Context_NoSuchBeanDefinitionException $ex) {
			$this->getLog()->info('no RequestBuilder configured, using DefaultRequestBuilder');
			$this->requestBuilder = new DefaultRequestBuilder();
		}

		self::$currentRequest = $this->requestBuilder->buildRequestObject();

		$this->handlerMapping = $this->context->getBean(self::HANDLER_MAPPING_BEAN_NAME, 'Bee\MVC\IHandlerMapping');
		$this->viewResolver = $this->context->getBean(self::VIEW_RESOLVER_BEAN_NAME, 'Bee_MVC_IViewResolver');

		try {
			$this->filterChainProxy = $this->context->getBean(self::FILTER_CHAIN_PROXY_NAME, 'Bee_MVC_IFilter');
		} catch (Bee_Context_NoSuchBeanDefinitionException $ex) {
			$this->getLog()->info('no filter chain proxy configured');
		}

		try {
			$this->handlerExceptionResolver = $this->context->getBean(self::HANDLER_EXCEPTION_RESOLVER_NAME, 'Bee_MVC_IHandlerExceptionResolver');
		} catch (Bee_Context_NoSuchBeanDefinitionException $ex) {
			$this->getLog()->info('no exception resolver configured');
		}
	}

	/**
	 *
	 * @return Bee_IContext
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

//		Bee_Cache_Manager::shutdown();
	}

	public function doFilter(Bee_MVC_IHttpRequest $request) {
		$this->dispatchInternally($request);
	}

	private function dispatchInternally(Bee_MVC_IHttpRequest $request) {
		$handler = null;
		try {
			$mav = null;
			$interceptors = array();
			$handlerException = null;
			try {

				$mappedHandler = $this->handlerMapping->getHandler($request);
				$interceptors = $mappedHandler->getInterceptors();
				$handler = $mappedHandler->getHandler();

//				$interceptorIndex = -1;

				for ($i = 0; $i < count($interceptors); $i++) {
					$interceptor = $interceptors[$i];
					if (!$interceptor->preHandle($request, $handler)) {
						//				$this->triggerAfterCompletion($handler, $interceptorIndex, $request, null);
						return;
					}
//					$interceptorIndex = $i;
				}

				// @todo: introduce HandlerAdapter
				$mav = $handler->handleRequest($request);

			} catch (Exception $e) {
				$this->getLog()->warn('handler or interceptor exception caught, trying to resolve appropriate error view', $e);
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

			if ($mav instanceof Bee_MVC_ModelAndView) {
				$mav->addModelValue(Bee_MVC_Model::CURRENT_REQUEST_KEY, $request);
				$this->resolveModelAndView($mav, $request);

				if(!is_null($handlerException) && !count($interceptors)) {
					// We were unable to resolve a handler and its interceptors due to an exception being thrown along
					// the way, but we have an error view. Assume the error view needs the interceptor post-handlers to
					// run normally: fetch list of configured interceptors from handler mapping directly.
					$interceptors = $this->handlerMapping->getInterceptors();
				}
				// Apply postHandle methods of registered interceptors.
				for ($i = count($interceptors) - 1; $i >= 0; $i--) {
					$interceptor = $interceptors[$i];
					$interceptor->postHandle($request, $handler, $mav);
				}

				$mav->renderModelInView();
			}
		} catch (Exception $e) {
			throw $e;
		}
	}

	public function resolveModelAndView(Bee_MVC_ModelAndView $mav, Bee_MVC_IHttpRequest $request) {
		$resolvedView = $this->viewResolver->resolveViewName($mav->getViewName(), $request);
		$mav->setResolvedView($resolvedView);
		if ($resolvedView instanceof Bee_MVC_View_Abstract) {
			$statics = $resolvedView->getStaticAttributes();
			if (!$statics) {
				$statics = array();
			}
			$model = array_merge($statics, $mav->getModel());
			$mav->setModel($model);
		}
		$this->resolveModelInternals($mav->getModel(), $request);
	}

	private function resolveModelInternals(array $model, Bee_MVC_IHttpRequest $request) {
		foreach ($model as $modelElem) {
			if ($modelElem instanceof Bee_MVC_ModelAndView) {
				$this->resolveModelAndView($modelElem, $request);
			} else if (is_array($modelElem)) {
				$this->resolveModelInternals($modelElem, $request);
			}
		}
	}
}

class B_DISPATCHER extends Bee_MVC_Dispatcher {
	public static function subDispatch($pathInfo, array $params = null, $method = null) {
		self::includeDispatch(Bee_MVC_HttpRequest::constructRequest(MODEL::get(MODEL::CURRENT_REQUEST_KEY), $pathInfo, $params, $method));
	}

	public static function subDispatchFromModel($pathInfo, array $modelKeys = null, $method = null) {
		$params = MODEL::getModel();
		if (is_array($modelKeys)) {
			$params = array_intersect_key($params, array_flip($modelKeys));
		}
		self::includeDispatch(Bee_MVC_HttpRequest::constructRequest(MODEL::get(MODEL::CURRENT_REQUEST_KEY), $pathInfo, $params, $method));
	}
}