<?php
require_once($_SERVER['DOCUMENT_ROOT']."/php/HyipCore.php");

	$plans = $HyipCore->arr_GetPlans();

	echo json_encode($plans);
?>