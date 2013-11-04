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

class Bee_MVC_View_Smarty extends Bee_MVC_View_Text {
	
	/**
	 * 
	 * @var Smarty
	 */
	private $smarty;
	
	/**
	 * 
	 * @var string
	 */
	private $template;
	
	/**
	 * 
	 * @var unknown_type
	 */
	private $localizedConfigFiles;

	/**
	 * 
	 * @var string
	 */
	private $localeModelKey;
	
	private $configFilesLoaded = false;

	/**
	 * 
	 * @return Smarty
	 */
	public function getSmarty() {
		return $this->smarty;
	}
	
	/**
	 * 
	 * @param Smarty $smarty
	 * @return void
	 */
	public function setSmarty(Smarty $smarty) {
		$this->smarty = $smarty;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getTemplate() {
		return $this->template;
	}
	
	/**
	 * 
	 * @param string $template
	 * @return void
	 */
	public function setTemplate($template) {
		$this->template = $template;
	}
	
	/**
	 * 
	 * @return array
	 */
	public function getLocalizedConfigFiles() {
		return $this->localizedConfigFiles;
	}

	/**
	 * 
	 * @param array $localizedConfigFiles
	 * @return void
	 */
	public function setLocalizedConfigFiles(array $localizedConfigFiles) {
		$this->localizedConfigFiles = $localizedConfigFiles;
	}

	/**
	 * 
	 * @return string
	 */
	public function getLocaleModelKey() {
		return $this->localeModelKey;
	}

	/**
	 * 
	 * @param string $localeModelKey
	 * @return void
	 */
	public function setLocaleModelKey($localeModelKey) {
		$this->localeModelKey = $localeModelKey;
	}

	public function afterPropertiesSet() {
	}

	protected function renderMergedOutputModel() {
		if(!$this->smarty) {
			$this->smarty = new Smarty();
		}

		$smarty = $this->smarty;
		
		$modelValues = Bee_MVC_Model::getModelValues(); 

		if(!$this->configFilesLoaded && $this->localeModelKey && is_array($this->localizedConfigFiles)) {
			$locale = $modelValues[$this->localeModelKey];
			$configFiles = $this->localizedConfigFiles[$locale];
			if(is_array($configFiles)) {
				foreach($configFiles as $configFile) {
					$smarty->config_load($configFile);
				}
				$this->configFilesLoaded = true;
			}
		}
		
		foreach($modelValues as $key => $value) {
			$smarty->assign($key, $value);
		}
		$smarty->display($this->template);		
	}
	
}
?>