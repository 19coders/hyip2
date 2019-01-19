<!doctype xhtml>
<?php
	if (!empty($_POST)) {
		if (array_key_exists("do", $_POST)) {
			if ($_POST['do'] == "newMessage") {
				$HyipCore->void_addTicketMessage(0, $_POST["tid"], $_POST["text"]);
				$Core->SetTicketStatus($_POST["tid"]);
			}
		}
	}

	if (!array_key_exists("tid", $_GET)) {
?>
<html>
	<head>
		<title>Tickets</title>
		<link rel="stylesheet" href="assets/css/grids.css" />
		<link rel="stylesheet" href="assets/css/style.css" />
		<script src="assets/js/jquery.min.js"></script>
		<script src="assets/js/functions.js"></script>
		<script src="assets/js/main.js"></script>
	</head>
	
	<body class="horizontal-centerer">
		<div id="main-wrapper" class="panelpage">
			<div id="topMenu" >
				<div class="tab"><a href="/adminpanel" >Hyip Settings</a></div>
				<div class="tab active">
<?php
	$count = $Core->CountUnansweredTickets();
	if ($count > 0) {
		echo "<div class='topMsgIndicator' >".$count."</div>";
	}
?>
					<a href="/adminpanel/tickets" >Tickets</a>
				</div>
				<div class="tab"><a href="/adminpanel/accounts" >Accounts</a></div>
				<div class="tab"><a href="/adminpanel/settings" >Settings</a></div>
				
				<div id="loginWrapper" >
<?php
	$admin = $Core->GetAdminByUID($_SESSION["admin_id"]);
	echo $admin["login"];
?>
					<button class="button" onclick="document.location.href='/adminpanel/logout'" >Logout</button>
				</div>
			</div>
			<div id="content" >
				<table>
					<thead> <tr> <th>Login</th> <th>Title</th> <th>Date</th> </tr> </thead>
<?php
	$tickets = $Core->GetUnansweredTickets();
	foreach ($tickets as $k => $v) {
		if ($v["login"] == "") {
			$acc = $HyipCore->GetAccountById($v["user_id"]);
			echo "<tr> <td>".$v["email"]."</td> <td><a href='/adminpanel/tickets?tid=".$v["id"]."'>".$v["title"]."</a></td> <td>".(date("d.m.Y H:i", $v["date"]))."</td> </tr>";
		}
		else {
			$acc = $HyipCore->GetAccountById($v["user_id"]);
			echo "<tr> <td>".$acc["login"]."</td> <td><a href='/adminpanel/tickets?tid=".$v["id"]."'>".$v["title"]."</a></td> <td>".(date("d.m.Y H:i", $v["date"]))."</td> </tr>";
		}
	}
?>
				</table>
			</div>
		</div>
	</body>
</html>
<?php
	}
	else {
?>
<html>
	<head>
		<title>Ticket</title>
		<link rel="stylesheet" href="assets/css/grids.css" />
		<link rel="stylesheet" href="assets/css/style.css" />
		<script src="assets/js/jquery.min.js"></script>
		<script src="assets/js/functions.js"></script>
		<script src="assets/js/main.js"></script>
	</head>
	
	<body class="horizontal-centerer">
		<div id="main-wrapper" class="panelpage">
			<div id="topMenu" >
				<div class="tab"><a href="/adminpanel" >Hyip Settings</a></div>
				<div class="tab active">
<?php
	$count = $Core->CountUnansweredTickets();
	if ($count > 0) {
		echo "<div class='topMsgIndicator' >".$count."</div>";
	}
?>
					<a href="/adminpanel/tickets" >Tickets</a>
				</div>
				<div class="tab"><a href="/adminpanel/settings" >Settings</a></div>
				
				<div id="loginWrapper" >
<?php
	$admin = $Core->GetAdminByUID($_SESSION["admin_id"]);
	echo $admin["login"];
?>
					<button class="button"><a href="/adminpanel/logout" >Logout</a></button>
				</div>
			</div>
			<div id="content" >
<?php
	$ticket = $HyipCore->arr_GetTicketByTID($_GET["tid"]);
	$acc = "";
	if ($ticket["owner"] == 2 || $ticket["owner"] == 0) {
	}
	else {
		$acc = $HyipCore->GetAccountById($ticket["user_id"]);
	}
	
	echo "<h2>" . $ticket["title"] . "</h2>";
?>

			<div class="message-wrapper">
				<form action="" method="POST" id="newMessage_form" >
					<input type="hidden" name="do" value="newMessage" />
					<input type="hidden" name="tid" value="<?php echo $_GET["tid"]; ?>" />
					
					<textarea name="text" class="field"></textarea>
					<button style="float: right;" class="button" type="submit" onclick="return checkNewMessageForm()" >Send</button>
				</form>
			</div>

<?php 
	$tid_messages = $HyipCore->arr_GetTicketMsgsByTID($_GET["tid"]);
	
	$left_message_block = 
	"
			<div class='message-wrapper' >
				<div class='message leftMessage' >
					<div class='messageTopBar' >%login%, %date%</div>
					%text%
				</div>
			</div>
	";
		
	$right_message_block = 
	"
			<div class='message-wrapper' >
				<div class='message rightMessage' >
					<div class='messageTopBar' >%login%, %date%</div>
					%text%
				</div>
			</div>
	";
	
	foreach($tid_messages as $value) {
	//	echo $ticket["owner"], "<br />", $ticket["email"];
		if($value["owner"] == 0) {
			$html_tickets .= str_replace
			(
				array("%login%", "%date%", "%text%"), 
				array("Administration", date("d.m.Y H:i", $value["date"]), $value["text"]),
				$right_message_block
			);
		}
		else if ($value["owner"] == 2) {
			$html_tickets .= str_replace
			(
				array("%login%", "%date%", "%text%"), 
				array($ticket["email"], date("d.m.Y H:i", $value["date"]), $value["text"]),
				$left_message_block
			);
		}
		else {
			$html_tickets .= str_replace
			(
				array("%login%", "%date%", "%text%"), 
				array($acc["login"], date("d.m.Y H:i", $value["date"]), $value["text"]),
				$left_message_block
			);
		}
	}
	
	echo $html_tickets;
?>
			</div>
		</div>
	</body>
</html>
<?php
	}
?>