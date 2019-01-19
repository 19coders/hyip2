<?php
require_once($_SERVER['DOCUMENT_ROOT']."/php/HyipCore.php");

	session_start();
	
	if(!array_key_exists("uid", $_SESSION)) {
		echo json_encode(array("unauthorized"));
		exit();
	}
	
	$config = new AdmConfig();
	try { $Core = new HyipCore(); } catch (DBConnectionException $e) { echo $e->getMessage(); }
	
	$wallets = $Core->arr_GetWalletsByUID($_SESSION["uid"]);
	
	foreach ($wallets as $key => $value) {
		unset($wallets[$key]["user_id"]);
		unset($wallets[$key]["balance"]);
		$wallets[$key]["payment_system_name"] = $Core->s_GetPaymentSystemNameByID($wallets[$key]["payment_system_id"]);
		$wallets[$key]["name"] = $wallets[$key]["payment_system_name"] . " " . $wallets[$key]["wallet"];
		unset($wallets[$key]["payment_system_id"]);
	}
	
	echo json_encode($wallets);
?>