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

class Bee_Text_EncodingConverter {
	
	const UNICODE_NORMALIZATION_DECOMPOSED = 'NFD';  // Canonical Decomposition
	const UNICODE_NORMALIZATION_COMPOSED = 'NFC'; // Canonical Decomposition, followed by Canonical Composition
	const UNICODE_NORMALIZATION_COMPATIBLE_DECOMPOSED = 'NFKD';  // Compatibility Decomposition
	const UNICODE_NORMALIZATION_COMPATIBLE_COMPOSED = 'NFKC'; // Compatibility Decomposition, followed by Canonical Composition
	
	const DEFAULT_INTERNAL_ENCODING = 'UTF-8';
	const DEFAULT_INTERNAL_UNICODE_NORMALIZATION = self::UNICODE_NORMALIZATION_COMPOSED;
	
	const DEFAULT_EXTERNAL_ENCODING = 'UTF-8';
	const DEFAULT_EXTERNAL_UNICODE_NORMALIZATION = self::UNICODE_NORMALIZATION_COMPOSED;

	/**
	 * Enter description here...
	 *
	 * @var I18N_UnicodeNormalizer
	 */
	private $normalizer;
	
	/**
	 * Enter description here...
	 *
	 * @var string
	 */
	private $internalEncoding = self::DEFAULT_INTERNAL_ENCODING;
	
	/**
	 * Enter description here...
	 *
	 * @var string
	 */
	private $internalUnicodeNormalization = self::DEFAULT_INTERNAL_UNICODE_NORMALIZATION;
	
	/**
	 * Enter description here...
	 *
	 * @var string
	 */
	private $externalEncoding = self::DEFAULT_EXTERNAL_ENCODING;
	
	/**
	 * Enter description here...
	 *
	 * @var string
	 */
	private $externalUnicodeNormalization = self::DEFAULT_EXTERNAL_UNICODE_NORMALIZATION;
	
	/**
	 * Enter description here...
	 *
	 */
	public function __construct() {
		$this->normalizer = new I18N_UnicodeNormalizer();
	}
	
	/**
	 * Enter description here...
	 *
	 * @return string
	 */
	public final function getInternalEncoding() {
		return $this->internalEncoding;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param string $encoding
	 * @return void
	 */
	public final function setInternalEncoding($encoding) {
		$this->internalEncoding = Bee_Utils_Strings::hasText($encoding) ? $encoding : self::DEFAULT_INTERNAL_ENCODING;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return string
	 */
	public final function getInternalUnicodeNormalization() {
		return $this->internalUnicodeNormalization;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param string $encoding
	 * @return void
	 */
	public final function setInternalUnicodeNormalization($normalization) {
		$this->internalUnicodeNormalization = Bee_Utils_Strings::hasText($normalization) ? $normalization : self::DEFAULT_INTERNAL_UNICODE_NORMALIZATION;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return string
	 */
	public final function getExternalEncoding() {
		return $this->externalEncoding;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param string $encoding
	 * @return void
	 */
	public final function setExternalEncoding($encoding) {
		$this->externalEncoding = Bee_Utils_Strings::hasText($encoding) ? $encoding : self::DEFAULT_EXTERNAL_ENCODING;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return string
	 */
	public final function getExternalUnicodeNormalization() {
		return $this->externalUnicodeNormalization;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param string $encoding
	 * @return void
	 */
	public final function setExternalUnicodeNormalization($normalization) {
		$this->externalUnicodeNormalization = Bee_Utils_Strings::hasText($normalization) ? $normalization : self::DEFAULT_EXTERNAL_UNICODE_NORMALIZATION;
	}
	
	public final function encode($value) {
		return $this->transcode($value, $this->internalEncoding, $this->externalEncoding, $this->externalUnicodeNormalization);
	}
	
	public final function decode($value) {
		return $this->transcode($value, $this->externalEncoding, $this->internalEncoding, $this->internalUnicodeNormalization);
	}
	
	public final function transcode($value, $fromEnc, $toEnc, $toNorm = false) {
		if($fromEnc !== $toEnc) {
			if($this->isUnicodeEncoding($fromEnc)) {
				// iconv apparently needs NFC input to process it correctly
	 			$value = $this->normalizer->normalize($value, 'NFC');
			}
			$coded = iconv($fromEnc, $toEnc, $value);
			if($coded) {
				$value = $coded;			
			} else {
				trigger_error("Iconv could not convert string from $fromEnc to $toEnc", E_USER_WARNING); 
			}
		}
		if($toNorm && $this->isUnicodeEncoding($toEnc)) {	
			$value = $this->normalizer->normalize($value, $toNorm);
		}
		return $value;		
	}
	
	public final function isUnicodeEncoding($enc) {
		return stripos($enc, 'utf') !== false || stripos($enc, 'ucs') !== false;
	}
}
?>