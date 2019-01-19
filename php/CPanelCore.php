<?php
require_once($_SERVER['DOCUMENT_ROOT']."/php/DB.class.php");

	class CPanelCore {
		private $db;
		
		public function __construct(){
			$this->db = new DB();
		}
		
		/*
		 *		Авторизация
		 */
		public function CheckAdmin($login, $password) {
			return $this->db->checkRow
			(
				"admins", 
				array (
					array("login", "=", $login),
					array("passhash", "=", md5($password . "salt salt salt"))
				)
			);
		}
		
		public function GetAdmin($login) {
			$rows = $this->db->getRows(
				"admins",
				array (
					array("login", "=", $login)
				)
			);
			return $rows[0];
		}
		
		public function GetAdminByUID($id) {
			$rows = $this->db->getRows(
				"admins",
				array (
					array("id", "=", $id)
				)
			);
			return $rows[0];
		}
		
		public function UpdateAdmin($id, $login, $password) {
			$this->db->updateRow(
				"admins", 
				array (
					array("id", "=", $id)
				),
				array (
					array("login", "=", $login),
					array("passhash", "=", md5($password . "salt salt salt"))
				)
			);
		}
		/*
		 *		/Авторизация
		 */
		 
		/*
		 *		Конфиг
		 */
		public function GetConfigs() {
			return $this->db->getListRows("config");
		}
		/*
		 *		/Конфиг
		 */
		
		/*
		 *		/Валюты
		 */
		public function int_GetCurrencyIdByName($name){
			$rows = $this->db->getRows(
				"currencies", 
				array (
					array("name", "=", $name)
				)
			);
			return $rows[0]["id"];
		}
		/*
		 *		/Валюты
		 */
		 
		/*
		 *		Таблицы
		 */
		public function EditRow($table, $id, $fieldsAndValues) {
			if ($id == 0) { self::AddRow($table, $fieldsAndValues); return; }
			
			if ($table == "plans") { 
				$hours = ($fieldsAndValues["delay_between_payments"]);
				$fieldsAndValues["delay_between_payments"] = $hours * 3600;
			}
			
			$newFieldsAndValues = array();
			
			foreach ($fieldsAndValues as $k => $v) {
				$newFieldsAndValues[] = array($k, "=", $v);
			}
			
			$this->db->updateRow(
				$table, 
				array (
					array("id", "=", $id)
				),
				$newFieldsAndValues
			);
		}
		
		public function DeleteRow($table, $id) {
			$this->db->deleteRow(
				$table, 
				array (
					array("id", "=", $id)
				)
			);
		}
		public function AddRow($table, $fieldsAndValues) {
			if ($table == "payment_systems") { 
				$cid = self::int_GetCurrencyIdByName($fieldsAndValues["currency_id"]);
				$fieldsAndValues["currency_id"] = $cid;
			}
			if ($table == "plans") { 
				$hours = ($fieldsAndValues["delay_between_payments"]);
				$fieldsAndValues["delay_between_payments"] = $hours * 3600;
			}
			// if ($table == "currencies") { 
				// $cid = self::int_GetCurrencyIdByName($fieldsAndValues["currency_id"]);
				// $fieldsAndValues["currency_id"] = $cid;
			// }
			
			$newFieldsAndValues = array();
			
			foreach ($fieldsAndValues as $k => $v) {
				$newFieldsAndValues[] = array($k, "=", $v);
			}
			
			echo $this->db->addRow(
				$table,
				$newFieldsAndValues
			);
		}
		/*
		 *		/Таблицы
		 */
		
		/*
		 *		Тикеты
		 */
		public function GetUnansweredTickets() {
			$rows = $this->db->getRows(
				"tickets",
				array (
					array("status", "=", 0)
				)
			);
			return $rows;
		}
		public function CountUnansweredTickets() {
			return $this->db->countRows(
				"tickets",
				array (
					array("status", "=", 0)
				)
			);
		}
		public function SetTicketStatus($tid) {
			$this->db->updateRow(
				"tickets",
				array (
					array("id", "=", $tid)
				),
				array (
					array("status", "=", 1)
				)
			);
		}
		/*
		 *		/Тикеты
		 */
	}
?>