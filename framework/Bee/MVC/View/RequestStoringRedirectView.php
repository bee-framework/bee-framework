<?php
namespace Bee\MVC\View;
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

/**
 * Class RequestStoringRedirectView
 * @package Bee\MVC\View
 */
class RequestStoringRedirectView extends RedirectView {
    use TRequestStoringView;

    /**
     * @param array $model
     */
    public function render(array $model = array()) {
        $getParams = array_key_exists(RedirectView::MODEL_KEY_GET_PARAMS, $model) ? $model[RedirectView::MODEL_KEY_GET_PARAMS] : array();
        $model[RedirectView::MODEL_KEY_GET_PARAMS] = array_merge($getParams, $this->createStoreParams($model));
        parent::render($model);
    }
}