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
 * Interface to be implemented by beans that need to react once all their
 * properties have been set by a Context: for example, to perform custom
 * initialization, or merely to check that all mandatory properties have been set.
 * 
 * <p>An alternative to implementing InitializingBean is specifying a custom
 * init-method, for example in an XML bean definition.
 * For a list of all bean lifecycle methods, see the BeanFactory javadocs.
 *
 * @see Bee_Context_Config_IBeanNameAware
 * @see Bee_Context_Config_IContextAware
 * 
 * @author Benjamin Hartmann
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
interface Bee_Context_Config_IInitializingBean {
	
	/**
	 * Invoked by a Context after it has set all bean properties supplied
	 * (and satisfied ContextAware).
	 * <p>This method allows the bean instance to perform initialization only
	 * possible when all bean properties have been set and to throw an
	 * exception in the event of misconfiguration.
	 * @throws Exception in the event of misconfiguration (such
	 * as failure to set an essential property) or if initialization fails.
	 */
	public function afterPropertiesSet();
}