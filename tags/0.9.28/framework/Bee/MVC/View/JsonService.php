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
 * Simply encodes the model array into a JSON string and sends it to the client. Useful for implementing JSON services.
 * 
 * @author Michael Plomer <michael.plomer@iter8.de>
 */
class Bee_MVC_View_JsonService extends Bee_MVC_View_Abstract {

//	protected function prepareResponse() {
//	}
	
	/**
	 * @Return void
	 * @Param model Array
	 */
	protected function renderMergedOutputModel() {
		echo json_encode(Bee_MVC_Model::getModelValues());
	}	
}