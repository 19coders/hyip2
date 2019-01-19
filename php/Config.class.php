<?php
	class Config {
		private $Config = array();
		private $configPath;
		
		public function __construct() {
			$this->configPath = $_SERVER['DOCUMENT_ROOT']."/php/config.json";
			self::_getConfigFromFile();
		}
		
		private function _getConfigFromFile() {
			$JsonConfig = "";
			
			if (file_exists($this->configPath)) $JsonConfig = file_get_contents($this->configPath);
			else file_put_contents($this->configPath, "");
			
			if ($JsonConfig == "") {}
			else $this->Config = json_decode($JsonConfig, true);
		}
		
		private function _saveConfigToFile() {
			file_put_contents($this->configPath, json_encode($this->Config));
		}
		
		private function _changeConfig($ParamName, $ParamVal) {
			$this->Config[$ParamName] = $ParamVal;
			self::_saveConfigToFile();
		}
		
		private function _getConfig($ParamName) {
			return $this->Config[$ParamName];
		}
		
		public function __get($Name) {
			return self::_getConfig($Name);
		}
		
		public function __set($Name, $Value) {
			self::_changeConfig($Name, $Value);
		}
	}
?>