<?php
require_once($_SERVER['DOCUMENT_ROOT']."/php/Config.class.php");

//error_reporting(E_ERROR);

class DBConnectionException extends Exception {
	public function __construct($desc, $code) {
		parent::__construct($desc, $code);
	}
}
	class WrongDBHostExcpt extends DBConnectionException{
		public function __construct() {
			parent::__construct("Wrong Database Host", 2002);
		}
	}
	class WrongDBUserExcpt extends DBConnectionException{
		public function __construct() {
			parent::__construct("Wrong Database Username or Password", 1044);
		}
	}
	class WrongDBNameExcpt extends DBConnectionException{
		public function __construct() {
			parent::__construct("Wrong Database Name", 1049);
		}
	}

	class Connection {
		private $mysqli;
		private $config;
		
		public function __construct() {
			$this->config = new Config();
			$this->mysqli = @new mysqli($this->config->db_host, $this->config->db_userName, $this->config->db_userPass, $this->config->db_dbName);
			
			if ($this->mysqli->connect_errno != 0) {
				if ($this->mysqli->connect_errno == 2002) throw new WrongDBHostExcpt();
				else if ($this->mysqli->connect_errno == 1044) throw new WrongDBUserExcpt();
				else if ($this->mysqli->connect_errno == 1049) throw new WrongDBNameExcpt();
			}
			else {
				$this->mysqli->query("SET lc_time_names = 'ru_RU'");
				$this->mysqli->query("SET NAMES 'utf8'");
			}
		}
		
		public function select_db($dbName) {
			$this->mysqli->select_db($dbName);
		}
		
		public function _getLastError() {
			return $this->mysqli->connect_errno;
		}
		
		public function query($query){
			return $this->mysqli->query($query);
		}
		
		public function escapeString($str){
			return $this->mysqli->real_escape_string($str);
		}
		
		public function getLastInsertId() {
			return $this->mysqli->insert_id;
		}
		
		public function __destruct() {
			if ($this->mysqli) $this->mysqli->close();
		//	echo $this->mysqli->connect_errno;
		//	echo $this->mysqli->connect_error;
		}
	}
?>