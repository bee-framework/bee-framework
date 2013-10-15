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
 */
interface Bee_Beans_ITypeConverter {
	
	/**
	 * Convert the value to the required type (if necessary from a String).
	 * <p>Conversions from String to any type will typically use the <code>setAsText</code>
	 * method of the PropertyEditor class. Note that a PropertyEditor must be registered
	 * for the given class for this to work; this is a standard JavaBeans API.
	 * A number of PropertyEditors are automatically registered.
	 * 
	 * 
	 * @param mixed $value the value to convert
	 * @param String $requiredTypeName the type we must convert to
	 * (or <code>null</code> if not known, for example in case of a collection element)
	 * @return mixed the new value, possibly the result of type conversion
	 * @throws TypeMismatchException if type conversion failed
	 */
	public function convertIfNecessary($value, $requiredTypeName);
	
}
?>