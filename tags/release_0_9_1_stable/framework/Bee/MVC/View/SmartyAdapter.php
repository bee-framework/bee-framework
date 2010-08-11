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

class Bee_MVC_View_SmartyAdapter extends Smarty implements Bee_Context_Config_IInitializingBean {
	
	/**
	 * 
	 * @var array
	 */
	private $configFiles;
	
	public function getTemplateDir() {
		return $this->template_dir;
	}
	
	public function setTemplateDir($templateDir) {
		$this->template_dir = $templateDir;
	}
	
	public function getCompileDir() {
		return $this->compile_dir;
	} 
	
	public function setCompileDir($compileDir) {
		$this->compile_dir = $compileDir;
	}
	
	public function getConfigDir() {
		return $this->config_dir;
	}
	
	public function setConfigDir($configDir) {
		$this->config_dir = $configDir;
	}
	
	public function getCacheDir() {
		return $this->cache_dir;
	}
	
	public function setCacheDir($cacheDir) {
		$this->cache_dir = $cacheDir;
	}
	
	public function getPluginsDir() {
		return $this->plugins_dir;
	}
	
	public function setPluginsDir($pluginsDir) {
		$this->plugins_dir = $pluginsDir;
	}
	
	public function getConfigFiles() {
		return $this->configFiles;
	}
	
	public function setConfigFiles(array $configFiles) {
		$this->configFiles = $configFiles;
	}

	public function getConfigFile() {
		if(is_array($this->configFiles) && count($this->configFiles) > 0) {
			return $this->configFiles[0];
		}
		return false;
	}
	
	public function setConfigFile($configFile) {
		if(is_array($configFile)) {
			$this->setConfigFiles($configFile);
		} else {
			$this->configFiles = array($configFile);
		}
	}
	
	public function afterPropertiesSet() {
		$this->config_overwrite = FALSE;
		foreach($this->configFiles as $configFile) {
			$this->config_load($configFile);
		}
	}
}
?>