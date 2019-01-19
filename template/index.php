<?php
require_once($_SERVER['DOCUMENT_ROOT']."/php/HyipCore.php");
require_once($_SERVER['DOCUMENT_ROOT']."/php/HyipPageController.class.php");

	ini_set('log_errors', 'On');
	ini_set('error_log', $_SERVER['DOCUMENT_ROOT']."/php_errors.log");
	
	ini_set('fix_pathinfo', 0);
	
	
function checkSignin() {
	return array_key_exists("uid", $_SESSION);
}

function hardCheckSignin() {
	if (!checkSignin()) { header("Location: /signin"); exit(); }
}
	
	session_start();
	
	$url_array = parse_url($_SERVER['REQUEST_URI']);
	$PATH = $url_array["path"];
	if (substr($PATH, -1) == "/") $PATH = substr($PATH, 0, -1);
	
	$config = new Config();
	try { $Core = new HyipCore(); } catch (DBConnectionException $e) { echo $e->getMessage(); exit();}
	
	/////////////////////////////////////////////
	// $Core->void_UpdateAllDeposits();
	/////////////////////////////////////////////
	
	$_ERRORS = array(
		0 => "OK", 
		1 => "Неверное имя пользователя или пароль!",
		2 => "Некорректный e-mail!",
		3 => "Имя пользователя занято!",
		4 => "Пароли не совпадают!",
		5 => "E-mail занят!",
		6 => "Недостаточно средств на счету!",
		7 => "Сумма депозита меньше минимальной!",
		8 => "Сумма депозита больше максимальной!",
		9 => "Идентификатор транзакции уже использован!"
	);
	
	$_SESSION["ERROR"] = 0;
	


	if (!empty($_GET)) {
		if (array_key_exists("r", $_GET)) {
			if (!isset($_COOKIE["refer"])) {
				$user = $Core->GetAccount($_GET["r"]);
				$Core->void_IncrementRefViews($user["id"]);
				setcookie("refer", $user["id"], 1893445200); // 01.01.2030 00:00:00
			}
			header("Location: /" . $PATH);
		}
	}
	
	
	if (!empty($_POST)) {
		if (array_key_exists("do", $_POST)) {
			if ($_POST["do"] == "addWallet") {
				hardCheckSignin(); 
				$Core->void_addWallet($_SESSION["uid"], $_POST["payment_system"], $_POST["wallet"]);
			}
			else if ($_POST["do"] == "delWallet") {
				hardCheckSignin(); 
				$Core->void_deleteWallet($_POST["wid"]);
			}
			else if ($_POST["do"] == "editWallet") {
				hardCheckSignin(); 
				$Core->void_editWallet($_POST["wid"], $_POST["wallet"]);
			}
			else if ($_POST["do"] == "addDeposit") {
				hardCheckSignin(); 
				$_SESSION["ERROR"] = $Core->void_CreateDeposit($_SESSION["uid"], $_POST["plan"], $_POST["sum"]);
			}
			else if ($_POST["do"] == "addTicket") {
				hardCheckSignin(); 
				$Core->void_addTicketMessage(1, $Core->int_addTicket($_SESSION["uid"], $_POST["title"]), $_POST["text"]);
			}
			else if ($_POST["do"] == "help") {
				$_SESSION["ERROR"] = $Core->void_addTicketMessage(2, $Core->int_addTicket(0, $_POST["title"], $_POST["email"]), $_POST["text"]);
			}
			else if ($_POST["do"] == "newMessage") {
				hardCheckSignin(); 
				$Core->void_addTicketMessage(1, $_POST["tid"], $_POST["text"]);
				$Core->SetTicketStatus($_POST["tid"]);
			}
			else if ($_POST["do"] == "setTransId") {
				$_SESSION["ERROR"] = $Core->int_checkTransId($_POST["payment_system"], $_POST["trans_id"]);
				if ($_SESSION["ERROR"] == 0) $Core->void_addRefill($_SESSION["uid"], $_POST["payment_system"], $_POST["trans_id"]);
			}
			else if ($_POST["do"] == "closeRefill") {
				$Core->arr_ViewRefill($_POST["id"]);
			}
			else if ($_POST["do"] == "addWithdrawalBid") {
				$_SESSION["ERROR"] = $Core->int_addWithdrawal($_SESSION["uid"], $_POST["wallet"], $_POST["sum"]);
			}
		}
	}
	
	$PageCtrl = new HyipPageController($Core);
	
	switch ($PATH){
		case "": 
			echo $PageCtrl->GenerateIndex(); 
		break;
		
		case "/faq": 
			echo $PageCtrl->GenerateFaq(); 
		break;
		
		case "/rules": 
			echo $PageCtrl->GenerateRules(); 
		break;
		
		case "/help": 
			echo $PageCtrl->GenerateHelp(); 
		break;
		
		case "/signin": 
			if (checkSignin()) { header("Location: /cabinet"); exit(); } 
			else if (array_key_exists("do", $_POST)) {
				if ($_POST["do"] == "signin") 
				{
					if ($Core->CheckAccount($_POST["login"], $_POST["password"])) {
						$user = $Core->GetAccount($_POST["login"]);
						$_SESSION["uid"] = $user["id"];
						header("Location: /cabinet");
						exit();
					}
					else {
						$_SESSION["ERROR"] = 1;
					}
				}
			}
			
			echo $PageCtrl->GenerateAuth(); 
		break;
		
		case "/reg": 
			if (checkSignin()) { header("Location: /cabinet"); exit(); } 
			else if (array_key_exists("do", $_POST)) {
				if ($_POST["do"] == "reg")
				{
					if ($_POST["password"] == $_POST["password_repeat"]) {
						if ($Core->CheckLogin($_POST["login"])) {
							$_SESSION["ERROR"] = 3;
						}
						else if ($Core->CheckEmail($_POST["email"])) {
							$_SESSION["ERROR"] = 5;
						}
						else {
							$Core->AddAccount($_POST["login"], $_POST["password"], $_POST["email"], (isset($_COOKIE["refer"])) ? $_COOKIE["refer"] : null);
							
							$user = $Core->GetAccount($_POST["login"]);
							$Core->void_addBalance($user["id"]);
							$_SESSION["uid"] = $user["id"];
							header("Location: /cabinet");
							exit();
						}
					}
					else {
						$_SESSION["ERROR"] = 4;
					}
				}
			}
			
			echo $PageCtrl->GenerateReg(@$_POST["login"], @$_POST["email"]); 
		break;
			
		case "/cabinet":
			hardCheckSignin(); echo $PageCtrl->GenerateCabinet(); 
		break;
		
		case "/deposits":
			hardCheckSignin(); echo $PageCtrl->GenerateDeposits(); 
		break;
		
		case "/affilate":
			hardCheckSignin(); echo $PageCtrl->GenerateAffilate(); 
		break;
		
		case "/tickets":
			hardCheckSignin(); echo $PageCtrl->GenerateTickets(); 
		break;
		
		case "/ticket":
			hardCheckSignin(); echo $PageCtrl->GenerateTicket($_REQUEST["tid"]); 
		break;
		
		case "/withdrawal":
			hardCheckSignin(); echo $PageCtrl->GenerateWithdrawal(); 
		break;
		
		case "/signout":
			hardCheckSignin(); unset($_SESSION["uid"]); header("Location: /"); exit(); break;
		
		default: 
			echo $PageCtrl->GenerateError404(); break;
	}
?>