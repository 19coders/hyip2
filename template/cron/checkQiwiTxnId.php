<?php
require_once($_SERVER['DOCUMENT_ROOT']."/php/HyipCore.php");
require_once($_SERVER['DOCUMENT_ROOT']."/php/QiwiApi.php");

function multisearch($array, $val_name, $needle) {
	$result = false;
	foreach ($array as $key => $val) {
		if ($val[$val_name] == $needle) {
			$result = $key;
			break;
		}
	}
	return $result;
}
	
	$config = new AdmConfig();
	try { $Core = new HyipCore(); } catch (DBConnectionException $e) { echo $e->getMessage(); exit();}
	$Qiwi = new FindYanot\QiwiApi("380508254398", "b3192e417686a4b82305fe89120afbbb");
	
	$queryParams = array("rows" => 50, "operation" => "IN");
	$payments = $Qiwi->getPaymentsHistory($queryParams);
	$payments = $payments["data"];
	$awaiting_refills = $Core->GetAwaitingRefills();
	
	foreach($awaiting_refills as $refill){
		$transaction_index = multisearch($payments, "trmTxnId", $refill["transaction_id"]);
		if ($transaction_index !== false) {
			$transaction = $payments[$transaction_index];
		//	print_r($transaction);
			$Core->void_setRefillStatus($refill["id"], 1);
			$Core->void_setRefillAmount($refill["id"], $transaction["sum"]["amount"]);
			$Core->void_AddToBalance($refill["user_id"], $transaction["sum"]["amount"]);
		}
	}
?>