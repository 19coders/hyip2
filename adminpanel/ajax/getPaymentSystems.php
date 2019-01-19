<?php
require_once($_SERVER['DOCUMENT_ROOT']."/php/HyipCore.php");
	
	$paymentSystems = $HyipCore->arr_GetPaymentSystems();

	echo json_encode($paymentSystems);
?>