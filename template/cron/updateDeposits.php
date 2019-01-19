<?php
require_once($_SERVER['DOCUMENT_ROOT']."/php/HyipCore.php");

	$config = new AdmConfig();
	try { $Core = new HyipCore(); } catch (DBConnectionException $e) { echo $e->getMessage(); exit();}
	
	$Core->void_UpdateAllDeposits();
?>