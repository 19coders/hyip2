<?php
require_once($_SERVER['DOCUMENT_ROOT']."/php/CPanelCore.php");
require_once($_SERVER['DOCUMENT_ROOT']."/php/HyipCore.php");

function checkParam($param) {
	$formatted = $param;
	$formatted = trim($formatted);
	$formatted = stripslashes($formatted);
	$formatted = htmlspecialchars($formatted);
	
	return $formatted;
}

	// if (!file_exists("installed")) {
		// header("Location: /adminpanel/install.php"); 
		// exit();
	// }
	
	// ini_set('display_errors', 'On');
	// ini_set('error_reporting', 'E_ALL');
	
	// error_reporting(E_ALL);

//	ini_set('log_errors', 'On');
//	ini_set('error_log', $_SERVER['DOCUMENT_ROOT']."/php_errors.log");
	
	session_start();
	
	$url_array = parse_url($_SERVER['REQUEST_URI']);
	$PATH = $url_array["path"];
	if (substr($PATH, -1) == "/") $PATH = substr($PATH, 0, -1);
	
	$config = new Config();
	try { $Core = new CPanelCore(); } catch (DBConnectionException $e) { echo $e->getMessage(); exit();}
	try { $HyipCore = new HyipCore(); } catch (DBConnectionException $e) { echo $e->getMessage(); exit();}

	if (!array_key_exists("admin_id", $_SESSION) && ($PATH !== "/adminpanel/auth")) header("Location: /adminpanel/auth");
	
	switch ($PATH){
		case "/adminpanel/auth": 
			if (array_key_exists("admin_id", $_SESSION)) { header("Location: /adminpanel"); exit(); } else include_once("template/panel_pages/auth.php"); break;
		case "/adminpanel/logout":
			unset($_SESSION["admin_id"]); header("Location: /adminpanel/auth"); break;
		case "/adminpanel": 
			include_once("template/panel_pages/index.php"); break;
		case "/adminpanel/tickets": 
			include_once("template/panel_pages/tickets.php"); break;
		case "/adminpanel/settings": 
			include_once("template/panel_pages/settings.php"); break;
		
		case "/adminpanel/editRow": 
			$Core->EditRow($_REQUEST["table"], $_REQUEST["id"], json_decode($_REQUEST["fieldsAndValues"], true)); break;
		case "/adminpanel/deleteRow": 
			$Core->DeleteRow($_REQUEST["table"], $_REQUEST["id"]); break;
		case "/adminpanel/addRow": 
			$Core->AddRow($_REQUEST["table"], $_REQUEST["id"], $_REQUEST["fieldsAndValues"]); break;
	}
?>