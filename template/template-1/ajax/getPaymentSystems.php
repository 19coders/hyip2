<?php
require_once($_SERVER['DOCUMENT_ROOT']."/php/HyipCore.php");

	session_start();
	
	if(!array_key_exists("uid", $_SESSION)) {
		echo json_encode(array("unauthorized"));
		exit();
	}
	
	$config = new AdmConfig();
	try { $Core = new HyipCore(); } catch (DBConnectionException $e) { echo $e->getMessage(); }
	
	$wallets = $Core->arr_GetPaymentSystems();

	echo json_encode($wallets);
?>