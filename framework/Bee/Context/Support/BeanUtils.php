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

abstract class Bee_Context_Support_BeanUtils {
	
	public static function instantiateClass($className, array $args = null) {
		$class = new ReflectionClass($className);
		if(is_null($args)||count($args) == 0) {
			return $class->newInstance();
		}
		return $class->newInstanceArgs($args);
	}

	public static function mergePropertyValuesIfPossible (Bee_Beans_PropertyValue $parent, Bee_Beans_PropertyValue $child) {
        $childValue = $child->getValue();
		if($childValue instanceof Bee_Context_Config_IMergeable && $childValue->getMergeEnabled() && $parent->getValue() instanceof Traversable) {
            $childValue->merge($parent->getValue());
		}
	}
}
