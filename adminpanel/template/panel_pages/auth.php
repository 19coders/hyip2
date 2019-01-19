<?php
	if (!empty($_POST)) {
		if (array_key_exists("do", $_POST)) {
			if ($_POST["do"] == "login") 
			{
				if ($Core->CheckAdmin($_POST["login"], $_POST["password"])) {
					$user = $Core->GetAdmin($_POST["login"]);
					$_SESSION["admin_id"] = $user["id"];
					$_SESSION["admin_login"] = $user["login"];
					header("Location: /adminpanel");
					exit();
				}
			}
		}
	}
?>

<!doctype xhtml>
<html>
	<head>
		<title>Authorization</title>
		<link rel="stylesheet" href="assets/css/grids.css" />
		<link rel="stylesheet" href="assets/css/style.css" />
	</head>
	
	<body class="horizontal-centerer vertical-centerer">
	
		<div id="main-wrapper" class="installpage">
			<form action="" method="post" id="" class="grid installpage">
				<h1>Authorization</h1>
				
				<div class="settings" id="settings_admpanel">
					<div class="grid settings-content">
						<div class="setting-field-caption" >Login</div> 
						<div class="setting-field-wrapper" ><input class="field" type="text" name="login" /></div>
						<div class="setting-field-caption" >Password</div>
						<div class="setting-field-wrapper" ><input class="field" type="password" name="password" /></div>
					</div>
				</div>
				
				<div id="" class="button-wrapper horizontal-centerer" >
					<button id="" class="button" type="submit" name="do" value="login">
						Log In
					</button>
				</div>
			</form>
		</div>
		
	</body>
	
</html>