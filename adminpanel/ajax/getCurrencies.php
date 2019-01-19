<?php
require_once($_SERVER['DOCUMENT_ROOT']."/php/HyipCore.php");
	
	$currencies = $HyipCore->arr_GetCurrencies();

	echo json_encode($currencies);
?>