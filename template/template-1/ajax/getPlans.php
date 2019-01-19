<?php
require_once($_SERVER['DOCUMENT_ROOT']."/php/HyipCore.php");

	session_start();
	
	if(!array_key_exists("uid", $_SESSION)) {
		echo json_encode(array("unauthorized"));
		exit();
	}
	
	$config = new AdmConfig();
	try { $Core = new HyipCore(); } catch (DBConnectionException $e) { echo $e->getMessage(); }
	
	$plans = $Core->arr_GetPlans();

	foreach ($plans as $key => $value) {
		unset($plans[$key]["percent"]);
		unset($plans[$key]["delay_between_payments"]);
		unset($plans[$key]["payments"]);
	}
	
	echo json_encode($plans);
?>