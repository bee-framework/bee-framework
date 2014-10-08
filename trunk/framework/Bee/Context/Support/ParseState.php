<?php
namespace Bee\Context\Support;
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
 * Class ParseState
 * @package Bee\Context\Support
 */
class ParseState {

    /**
     * Internal {@link Stack} storage.
     */
    private $state;

    /**
     * Create a new <code>ParseState</code> with an empty {@link Stack}.
     */
    public function __construct() {
        $this->state = array();
    }

    /**
     * Add a new {@link Bee_Context_Support_ParseStateEntry} to the stack
     * @param ParseState_Entry $entry
     * @return void
     */
    public function push(ParseState_Entry $entry) {
        array_push($this->state, $entry);
    }

    /**
     * Remove an {@link Bee_Context_Support_ParseStateEntry} from the stack.
     */
    public function pop() {
        array_pop($this->state);
    }

    /**
     * Return the {@link Entry} currently at the top of the {@link Stack} or
     * <code>null</code> if the {@link Stack} is empty.
     */
    public function peek() {
        $count = count($this->state);
        return $count == 0 ? null : $this->state[$count-1];
    }

}

/**
 * Marker interface for entries into the {@link ParseState}.
 */
interface ParseState_Entry {

}