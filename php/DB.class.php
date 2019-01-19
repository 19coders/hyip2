<?php
require_once $_SERVER['DOCUMENT_ROOT']."/php/Connection.class.php";

class DB {
	private $conn;
	
	public function __construct() {
		$this->conn = new Connection();
	}
	
	function truncateTable($tblName){
		$result = $this->conn->query("TRUNCATE TABLE `".$tblName."`");
	}
	
	private function _prepareWhereParams($fieldsAndValues, $connector) {
		$counter = 0;
		$q = "";
		foreach ($fieldsAndValues as $value) {
			$q .= "`" . $this->conn->escapeString($value[0]) . "` " . $value[1] . " '" . $this->conn->escapeString($value[2]) . "'";
			$counter++;
			if ($counter != count($fieldsAndValues)) $q .= " ".$connector." ";
		}
		return $q;
	}
	
	function Query($q) {
		return $this->conn->query($q);
	}
	
	function CheckDBIsExist($DBName) {
		$result = self::Query("SHOW DATABASES LIKE '" . $DBName . "'");
		$row = $result->fetch_row();
		if ($row[0] == "") return false;
		else return true;
	}
	
	function CreateDB($DBName){
		$q = "CREATE DATABASE `" . $DBName . "`";
		self::Query($q);
	}
	
	function CreateTable($TblName, $TblFields){
		self::Query("DROP TABLE `" . $TblName . "` IF EXISTS");
		
		$q = "CREATE TABLE `" . $TblName . "` (";
		
		foreach ($TblFields as $value) {
			$q .= $value . ",";
		}
		$q = substr($q, 0, -1);
		$q .= ")";
		
		self::Query($q);
	}
	
	
	
	function getDBName(){
		$result = self::Query("SELECT DATABASE()");
		$row = $result->fetch_row();
		return $row[0];
	}
	
	function getTables(){
		$dbName = $this->getDBName();
		$result_set = self::Query("SHOW TABLES IN ".$dbName);
		$res_arr = array();
		$i = 0;
		while ($row = $result_set->fetch_assoc()) {
			$res_arr[$i] = $row["Tables_in_".$dbName];
			$i++;
		}
		return $res_arr;
	}
	
	function getFields($tblName){
		$dbName = $this->getDBName();
		$result_set = self::Query("SHOW COLUMNS IN `".$tblName."` IN ".$dbName);
		$res_arr = array();
		$i = 0;
		while ($row = $result_set->fetch_assoc()) {
			$res_arr[$i] = $row["Field"];
			$i++;
		}
		return $res_arr;
	}
	
	
	function getFieldSum($tblName, $field, $fieldsAndValues = null) {
		if ($fieldsAndValues == null) $q = "SELECT sum(`". $this->conn->escapeString($field) ."`) FROM `". $this->conn->escapeString($tblName) ."`";
		else $q = "SELECT sum(`". $this->conn->escapeString($field) ."`) FROM `". $this->conn->escapeString($tblName) ."` WHERE " . self::_prepareWhereParams($fieldsAndValues, "AND");
		
		// echo $q;
		
		$result_set = self::Query($q);
		while ($row = $result_set->fetch_assoc()) {
			return $row["sum(`".$field."`)"];
		}
	}
	
	function checkRow($tblName, $fieldsAndValues){
		$q = "SELECT COUNT(*) FROM `".$tblName."` WHERE " . self::_prepareWhereParams($fieldsAndValues, "AND");
		
		$result_set = self::Query($q);
		
		while ($row = $result_set->fetch_assoc()) {
			if ($row["COUNT(*)"] > 0) return true;
				else return false;
		}
	}
	
	function getRows($tblName, $fieldsAndValues){
		$q = "SELECT * FROM `".$tblName."` WHERE " . self::_prepareWhereParams($fieldsAndValues, "AND");
	
	//	echo $q . "<br />";
	
		$result_set = self::Query($q);
		
		$res_arr = array();
		$i = 0;
		
		$fields = $this->getFields($tblName);
		
		while ($row = $result_set->fetch_assoc()) {
			$temp_arr = array();
			
			for ($j = 0; $j < count($fields); $j++){
				$temp_arr[$fields[$j]] = $row[$fields[$j]];
			}
			
			$res_arr[$i] = $temp_arr;
			$i++;
		}
		return $res_arr;
	}
	
	function countRows($tblName, $fieldsAndValues){
		$q = "SELECT COUNT(*) FROM `".$tblName."` WHERE " . self::_prepareWhereParams($fieldsAndValues, "AND");
		// echo $q;
		$result_set = self::Query($q);
		
		while ($row = $result_set->fetch_assoc()) {
			return $row["COUNT(*)"];
		}
	}
	
	function countAllRows($tblName) {
		$result_set = self::Query("SELECT COUNT(*) FROM `". $this->conn->escapeString($tblName) ."` WHERE 1");
		
		while ($row = $result_set->fetch_assoc()) {
			return $row["COUNT(*)"];
		}
	}
	
	function getListRows($tblName, $sort = true, $orderBy = "id", $fieldsAndValues = false, $limit = false, $offset = 0, $number = 0){
		if ($sort == true) $sort = "ASC";
		else $sort = "DESC";
		
		$q = $q = "SELECT * FROM `".$tblName."`";
		
		if ($fieldsAndValues != false) {
			$q .= " WHERE " . self::_prepareWhereParams($fieldsAndValues, "AND");
		}
		
		$q .= " ORDER BY `".$orderBy."` ".$sort;
		
		if ($limit == true) $q .= " LIMIT ".$offset.",".$number;
		
	//	echo $q;
		
		$result_set = self::Query($q);
		
		$res_arr = array();
		$i = 0;
		
		$fields = $this->getFields($tblName);
		
		while ($row = $result_set->fetch_assoc()) {
			$temp_arr = array();
			
			for ($j = 0; $j < count($fields); $j++){
				$temp_arr[$fields[$j]] = $row[$fields[$j]];
			}
			
			$res_arr[$i] = $temp_arr;
			$i++;
		}
		
		return $res_arr;
	}
	
	function getLastRow($tblName, $fieldsAndValues = false){
		$q = "SELECT * FROM `".$tblName."` WHERE id=(SELECT MAX(id) FROM `".$tblName."` ";
		
		if ($fieldsAndValues != false) {
			$q .= "WHERE " . self::_prepareWhereParams($fieldsAndValues, "AND");
		}
		
		$q .= ")";
		
		$result_set = self::Query($q);
		
		while ($row = $result_set->fetch_assoc()) {
			return $row;
		}
	}
	
	function getUniqueValues($tblName, $field) {
		$q = "SELECT DISTINCT `". $this->conn->escapeString($field) ."` FROM `". $this->conn->escapeString($tblName) ."`";
		$result_set = self::Query($q);
		
		$uniqueValues = [];
		while ($row = $result_set->fetch_assoc()) {
			$uniqueValues[] = $row[$this->conn->escapeString($field)];
		}
		
		return $uniqueValues;
	}
	
	function countUniqueValues($tblName, $field) {
		$result_set = self::Query("SELECT COUNT(DISTINCT `". $this->conn->escapeString($field) ."`) FROM `". $this->conn->escapeString($tblName) ."`");
		
		while ($row = $result_set->fetch_assoc()) {
			return $row["COUNT(DISTINCT `". $this->conn->escapeString($field) ."`)"];
		}
	}
	
	function addRow($tblName, $fieldsAndValues) {
		$q = "INSERT INTO `".$tblName."` SET " . self::_prepareWhereParams($fieldsAndValues, ",");
		self::Query($q);
		return $this->conn->getLastInsertId();
	}
	
	function updateRow($tblName, $neededFieldsAndValues, $fieldsAndValues){
		$q = "UPDATE `".$tblName."` SET " . self::_prepareWhereParams($fieldsAndValues, ",");
		$q .= " WHERE " . self::_prepareWhereParams($neededFieldsAndValues, "AND");
		self::Query($q);
	}
	
	function deleteRow($tblName, $fieldsAndValues){
		$q = "DELETE FROM `".$tblName."` WHERE " . self::_prepareWhereParams($fieldsAndValues, "AND");
		self::Query($q);
	}

	function getMaxValue($tblName, $needed_field, $fieldsAndValues){
		$q = "SELECT max(`".$needed_field."`) FROM `".$tblName."` WHERE " . self::_prepareWhereParams($fieldsAndValues, "AND");
		$result_set = self::Query($q);
		while ($row = $result_set->fetch_assoc()) {
			return $row["max(`".$needed_field."`)"];
		}
	}
	
	function getMinValue($tblName, $needed_field, $field, $value){
		$q = "SELECT min(`".$needed_field."`) FROM `".$tblName."` WHERE " . self::_prepareWhereParams($fieldsAndValues, "AND");
		$result_set = self::Query($q);
		while ($row = $result_set->fetch_assoc()) {
			return $row["min(`".$needed_field."`)"];
		}
	}
}
?>