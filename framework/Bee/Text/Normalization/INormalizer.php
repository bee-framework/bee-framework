<?php
namespace Bee\Text\Normalization;
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
 * User: mp
 * Date: 29.04.13
 * Time: 12:09
 */

interface INormalizer {

	const UNICODE_NORMALIZATION_DECOMPOSED = 'NFD';  // Canonical Decomposition
	const UNICODE_NORMALIZATION_COMPOSED = 'NFC'; // Canonical Decomposition, followed by Canonical Composition
	const UNICODE_NORMALIZATION_COMPATIBLE_DECOMPOSED = 'NFKD';  // Compatibility Decomposition
	const UNICODE_NORMALIZATION_COMPATIBLE_COMPOSED = 'NFKC'; // Compatibility Decomposition, followed by Canonical Composition

	/**
	 * @param string $input
	 * @param string $form
	 * @return string
	 */
	public function normalize($input, $form = self::UNICODE_NORMALIZATION_COMPOSED);
}
