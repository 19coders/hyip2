<?php
	class TemplateEngine {
		private $tpl;
		private $data = array();
		
		public function __construct() {
		}
		
		public function getContentsFromFile($path) {
			$this->tpl = file_get_contents($path);
		}

		public function __get($name) {
			if (isset($this->data[$name])) return $this->data[$name];
			else return "";
		}

		public function __set($name, $value) {
			$this->data[$name] = $value;
		}

		public function delete($name) {
			unset($this->data[$name]);
		}

		public function clean() {
			$this->data = array();
		}

		public function generate($template) {
			foreach ($this->data as $key => $value)
				$template = str_replace("%" . $key . "%", $value, $template);
			
			return $template;
		}
	}
?>
