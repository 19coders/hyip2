<?php
require_once($_SERVER['DOCUMENT_ROOT']."/php/DB.class.php");

	// if (file_exists("installed")) {
		// header("Location: /adminpanel"); 
		// exit();
	// }
	
	if (isset($_POST["install"])) {
		$config = new Config();
		$config->db_host = $_REQUEST["db_host"];
		$config->db_userName = $_REQUEST["db_username"];
		$config->db_userPass = $_REQUEST["db_userpass"];
		$config->db_dbName = NULL;
		
		$db = new DB();
		if (!$db->CheckDBIsExist($_REQUEST["db_name"])) {
			$db->CreateDB($_REQUEST["db_name"]);
		}
		
		$config->db_dbName = $_REQUEST["db_name"];
		
		unset($db);
		$db = new DB();
		
		$db->CreateTable
		(
			"admins",
			array
			(
				"`id` int(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
				"`login` varchar(255) NOT NULL",
				"`passhash` varchar(255) NOT NULL"
			)
		);
		
		$db->addRow
		(
			"admins",
			array
			(
				array("id", "=", ""),
				array("login", "=", $_REQUEST["adm_username"]),
				array("passhash", "=", md5($_REQUEST["adm_userpass"] . "salt salt salt"))
			)
		);
		
		$db->CreateTable
		(
			"config",
			array
			(
				"`id` int(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
				"`param_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL",
				"`param_normalName` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL",
				"`param_value` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL",
			)
		);
		
		$db->addRow
		(
			"config",
			array
			(
				array("param_name", "=", "template"),
				array("param_normalName", "=", "Шаблон сайта"),
				array("param_value", "=", "1")
			)
		);
		
		$db->addRow
		(
			"config",
			array
			(
				array("param_name", "=", "main_currency_id"),
				array("param_normalName", "=", "Используемая валюта (идентификатор)"),
				array("param_value", "=", "1")
			)
		);
		
		$db->addRow
		(
			"config",
			array
			(
				array("param_name", "=", "round_value"),
				array("param_normalName", "=", "До скольки знаков округлять суммы"),
				array("param_value", "=", "2")
			)
		);
		
		// $db->addRow
		// (
			// "config",
			// array
			// (
				// array("param_name", "=", "commission"),
				// array("param_normalName", "=", "Комиссия на пополнение (%)"),
				// array("param_value", "=", "10")
			// )
		// );
		
		$db->CreateTable
		(
			"accounts",
			array
			(
				"`id` int(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
				"`login` varchar(255) NOT NULL",
				"`passhash` varchar(255) NOT NULL",
				"`email` text NOT NULL",
				"`reg_date` int(10) UNSIGNED NOT NULL",
				"`ban` int(1) UNSIGNED NOT NULL DEFAULT '0'",
				"`reason` text NOT NULL",
				"`ref_views` int(10) UNSIGNED NOT NULL",
				"`refer_id` int(10) UNSIGNED NULL",
				"`ref_money` float UNSIGNED NOT NULL DEFAULT '0'"
			)
		);
		
		$db->CreateTable
		(
			"payment_systems",
			array
			(
				"`id` int(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
				"`currency_id` int(10) UNSIGNED NOT NULL",
				"`name` varchar(255) NOT NULL"
			)
		);
		
		$db->CreateTable
		(
			"wallets",
			array
			(
				"`id` int(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
				"`user_id` int(10) UNSIGNED NOT NULL",
				"`payment_system_id` int(10) UNSIGNED NOT NULL",
				"`wallet` text NOT NULL",
				"`balance` float NOT NULL DEFAULT '0'"
			)
		);
		
		$db->CreateTable
		(
			"deposits",
			array
			(
				"`id` int(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
				"`user_id` int(10) UNSIGNED NOT NULL",
				"`plan_id` int(10) UNSIGNED NOT NULL",
				"`amount` float UNSIGNED NOT NULL",
				"`paid_out` float UNSIGNED NOT NULL DEFAULT '0'",
				"`return_amount` float UNSIGNED NOT NULL",
				"`start_date` int(10) UNSIGNED NOT NULL",
				"`end_date` int(10) UNSIGNED NOT NULL",
				"`payments` int(10) UNSIGNED NOT NULL DEFAULT '0'",
				"`full_payments` int(10) UNSIGNED NOT NULL",
				"`next_payment` int(10) UNSIGNED NOT NULL",
				"`status` int(1) UNSIGNED NOT NULL DEFAULT '0'"
			)
		);
		
		$db->CreateTable
		(
			"plans",
			array
			(
				"`id` int(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
				"`name` varchar(255) NOT NULL",
				"`min` int(10) UNSIGNED NOT NULL",
				"`max` int(10) UNSIGNED NOT NULL",
				"`percent` int(10) NOT NULL",
				"`delay_between_payments` int(10) UNSIGNED NOT NULL",
				"`payments` INT(10) UNSIGNED NOT NULL"
			)
		);
		
		$db->CreateTable
		(
			"withdrawals",
			array
			(
				"`id` int(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
				"`user_id` int(10) UNSIGNED NOT NULL",
			//	"`payment_system_id` int(10) UNSIGNED NOT NULL",
				"`wallet_id` int(10) UNSIGNED NOT NULL",
				"`amount` float UNSIGNED NOT NULL",
				"`date` int(10) UNSIGNED NOT NULL",
				"`status` int(1) UNSIGNED NOT NULL DEFAULT '0'"
			)
		);
		
		$db->CreateTable
		(
			"currencies",
			array
			(
				"`id` int(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
				"`name` varchar(255) NOT NULL",
				"`rate_to_main` float UNSIGNED NOT NULL"
			)
		);
		
		$db->CreateTable
		(
			"balances",
			array
			(
				"`id` int(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
				"`user_id` int(10) UNSIGNED NOT NULL",
				"`sum` float UNSIGNED NOT NULL DEFAULT '0'"
			)
		);
		
		$db->CreateTable
		(
			"ref_levels",
			array
			(
				"`id` int(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
				"`level` int(10) UNSIGNED NOT NULL",
				"`percent` int(10) UNSIGNED NOT NULL"
			)
		);
		
		$db->CreateTable
		(
			"tickets",
			array
			(
				"`id` int(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
				"`user_id` int(10) UNSIGNED NOT NULL",
				"`email` text NOT NULL",
				"`title` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL",
				"`date` int(10) UNSIGNED NOT NULL",
				"`status` int(1) UNSIGNED NOT NULL DEFAULT '0'"
			)
		);
		
		$db->CreateTable
		(
			"ticket_messages",
			array
			(
				"`id` int(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
				"`owner` int(1) UNSIGNED NOT NULL",
				"`ticket_id` int(10) UNSIGNED NOT NULL",
				"`text` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL",
				"`date` int(10) UNSIGNED NOT NULL"
			)
		);
		
		$db->CreateTable
		(
			"refills",
			array
			(
				"`id` int(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
				"`user_id` int(10) UNSIGNED NOT NULL",
				"`payment_system_id` int(10) UNSIGNED NOT NULL",
				"`transaction_id` text NOT NULL",
				"`amount` float UNSIGNED NOT NULL",
				"`status` int(1) UNSIGNED NOT NULL DEFAULT '0'",
				"`viewed` int(1) UNSIGNED NOT NULL DEFAULT '0'"
			)
		);
		
		// file_put_contents("installed", "");
		header("Location: /adminpanel/auth"); 
		exit();
	}
?>

<!doctype xhtml>
<html>
	<head>
		<title>Install</title>
		
		<link rel="stylesheet" href="assets/bootstrap/bootstrap.min.css" />
		
		<script src="assets/js/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
		<script src="assets/bootstrap/bootstrap.min.js"></script>
		
		<!--
		<link rel="stylesheet" href="assets/css/grids.css" />
		<link rel="stylesheet" href="assets/css/style.css" />
		-->
		
		<link rel="stylesheet" href="assets/css/index.css" />
	</head>
	
	<body class="vertical-centerer">
	
		<div class="container-fluid">
			<div class="row justify-content-center align-items-center">
				<div class="wrapper wrapper--installpage col col-xl-5 col-lg-6 col-md-7 col-sm-8">
				
					<form method="post" id="" class="mb-2 mt-2">
						<div class="row">
							<div class="col col-12">
								<h1>Panel Installer</h1>
							</div>
						</div>
						<div class="row">
							<div class="col col-12">
								<h2>MySQL</h2>
						
								<div class="row pb-1">
									<div class="col col-6">Host</div>
									<div class="col col-6"><input class="field" name="db_host" /></div>
								</div>
								<div class="row pb-1">
									<div class="col col-6">Database</div>
									<div class="col col-6"><input class="field" name="db_name" /></div>
								</div>
								<div class="row pb-1">
									<div class="col col-6">Username</div>
									<div class="col col-6"><input class="field" name="db_username" /></div>
								</div>
								<div class="row pb-1">
									<div class="col col-6">User Password</div>
									<div class="col col-6"><input class="field" name="db_userpass" /></div>
								</div>
							</div>
						</div>
					
						
						<div class="row">
							<div class="col col-12">
								<h2>Admin-Panel</h2>
								<div class="row pb-1">
									<div class="col col-6">Login</div>
									<div class="col col-6"><input class="field" name="adm_username" /></div>
								</div>
								<div class="row pb-1">
									<div class="col col-6">Password</div>
									<div class="col col-6"><input class="field" name="adm_userpass" /></div>
								</div>
							</div>
						</div>
						
						
						<div class="row mt-3">
							<div class="col col-12">
								<div class="horizontal-centerer" ><button class="button button--install" type="submit" name="install">Install</button></div>
							</div>
						</div>
					</form>
					
				</div>
			</div>
		</div>
		
	</body>
</html>