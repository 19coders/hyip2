<?php
require_once($_SERVER['DOCUMENT_ROOT']."/php/DB.class.php");

	$config = new AdmConfig();
	
	if (!empty($_POST)) {
		if (isset($_POST["updateDBSettings"])) {
			$config->db_host = $_POST["db_host"];
			$config->db_dbName = $_POST["db_name"];
			$config->db_userName = $_POST["db_username"];
			$config->db_userPass = $_POST["db_userpass"];
		}
		if (isset($_POST["updatePanelSettings"])) {
			
			if ($_POST["password"] != "") {
				$Core->UpdateAdmin($_SESSION["admin_id"], $_POST["login"], $_POST["password"]);
				$_SESSION["login"] = $_POST["login"];
			}
			
		}
		header("Location: /adminpanel/settings");
	}
?>

<!doctype xhtml>
<html>
	<head>
		<title>Settings</title>
		<link rel="stylesheet" href="assets/css/style.css" />
		<link rel="stylesheet" href="assets/css/grids.css" />
	</head>
	
	<body class="horizontal-centerer">
	
		<div id="main-wrapper" class="settingspage">
		
			<div id="topMenu" >
				<div class="tab"><a href="/adminpanel" >Hyip Settings</a></div>
				<div class="tab">
<?php
	$count = $Core->CountUnansweredTickets();
	if ($count > 0) {
		echo "<div class='topMsgIndicator' >".$count."</div>";
	}
?>
					<a href="/adminpanel/tickets" >Tickets</a>
				</div>
				<div class="tab"><a href="/adminpanel/accounts" >Accounts</a></div>
				<div class="tab active"><a href="/adminpanel/settings" >Settings</a></div>
				
				<div id="loginWrapper" >
<?php
	$admin = $Core->GetAdminByUID($_SESSION["admin_id"]);
	echo $admin["login"];
?>
					<button class="button" onclick="document.location.href='/adminpanel/logout'" >Logout</button>
				</div>
			</div>
			
			<div id="content" class="horizontal-centerer">
			<!--
				<h2>Settings</h2> -->
				<form method="post" class="grid mysqlGrid">
					<div class="settings" id="settings_mysql">
						<h2>MySQL Settings</h2>
						<div class="grid settings-content">
						
							<div class="setting-field-caption" >MySQL Status</div>
<?php if ($_ERROR) { ?>
							<div class="setting-field-wrapper" id="mysql-status_box" style="color: red;" >Disconnected</div>
<?php } else { ?>
							<div class="setting-field-wrapper" id="mysql-status_box" style="color: green;" >Connected</div>
<?php } ?>

							<div class="setting-field-caption" >Host</div> 
							<div class="setting-field-wrapper" ><input class="field" name="db_host" value="<?php echo $config->db_host; ?>" /></div>
							
							<div class="setting-field-caption" >Database</div> 
							<div class="setting-field-wrapper" ><input class="field" name="db_name" value="<?php echo $config->db_dbName; ?>" /></div>
							
							<div class="setting-field-caption" >Username</div> 
							<div class="setting-field-wrapper" ><input class="field" name="db_username" value="<?php echo $config->db_userName; ?>" /></div>
							
							<div class="setting-field-caption" >User Password</div> 
							<div class="setting-field-wrapper" ><input class="field" name="db_userpass" value="<?php echo $config->db_userPass; ?>" /></div>
						</div>
						
						<div id="" class="button-wrapper horizontal-centerer" >
							<button id="" class="button" type="submit" name="updateDBSettings" value="">
								Save
							</button>
						</div>
					</div>
				</form>
				
				<form method="post" class="grid mysettingsGrid">
					<div class="settings" id="settings_admpanel">
						<h2>My Settings</h2>
						<div class="grid settings-content">
							<div class="setting-field-caption" >Login</div> 
							<div class="setting-field-wrapper" ><input name="login" class="field" value="<?php echo $admin["login"]; ?>" /></div>
							
							<div class="setting-field-caption" >Password</div>
							<div class="setting-field-wrapper" ><input name="password" class="field" /></div>
						</div>

						<div id="" class="button-wrapper horizontal-centerer" >
							<button id="" class="button" type="submit" name="updatePanelSettings" value="">
								Save
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
		
	</body>
	
</html>