<?php
namespace Bee\MVC\Controller;
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
use Bee\MVC\IHttpRequest;
use Bee\MVC\ModelAndView;
use Bee\Utils\Assert;
use Bee_MVC_Controller_MultiActionMultiDelegates_IDelegateResolver;

/**
 * The multiaction controller handles requests by delegating to method calls on a delegate object. The methods to be used are
 * determined by the method name resolver.
 * 
 * A handler method must take at least one argument, type-hinted to Bee\MVC\IHttpRequest, and return a Bee\MVC\ModelAndView.
 * @todo: update phpdoc
 * 
 * @see IHttpRequest
 * @see ModelAndView
 * 
 * @author Benjamin Hartmann
 */
class MultiActionMultiDelegatesController extends MultiActionController {

    /**
     * @var Bee_MVC_Controller_MultiActionMultiDelegates_IDelegateResolver
     */
    private $delegateResolver;

    // determine what delegate to select

	/**
	 * Enter description here...
	 *
	 * @param IHttpRequest $request
	 * @return ModelAndView
	 */
    protected function handleRequestInternally(IHttpRequest $request) {
        $this->setDelegate($this->delegateResolver->resolveDelegate($request));
        return parent::handleRequestInternally($request);
    }

    /**
     * Gets the DelegateResolver
     *
     * @return Bee_MVC_Controller_MultiActionMultiDelegates_IDelegateResolver $delegateResolver
     */
    public function getDelegateResolver() {
        return $this->delegateResolver;
    }

    /**
     * Sets the DelegateResolver
     *
     * @param $delegateResolver Bee_MVC_Controller_MultiActionMultiDelegates_IDelegateResolver
     * @return void
     */
    public function setDelegateResolver(Bee_MVC_Controller_MultiActionMultiDelegates_IDelegateResolver $delegateResolver) {
        $this->delegateResolver = $delegateResolver;
    }

    /**
     * Enter description here...
     *
     * @return void
     */
    protected function init() {
		parent::init();
		Assert::notNull($this->delegateResolver, '\'methodNameResolver\' property is required in ' . __CLASS__);
    }

    /**
     * Enter description here...
     *
     * @return object
     */
    public function getDelegate() {
        if (is_null(parent::getDelegate())) {
            return $this->delegateResolver->getDefaultDelegate();
        }
        return parent::getDelegate();
    }
}