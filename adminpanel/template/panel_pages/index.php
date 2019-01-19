<!doctype xhtml>
<html>
	<head>
		<title>Hyip Settings</title>
		<link rel="stylesheet" href="assets/css/grids.css" />
		<link rel="stylesheet" href="assets/css/style.css" />
		<script src="assets/js/jquery.min.js"></script>
		<script src="assets/js/functions.js"></script>
		<script src="assets/js/main.js"></script>
	</head>
	
	<body class="horizontal-centerer">
		<div id="main-wrapper" class="panelpage">
			<div id="topMenu" >
				<div class="tab active"><a href="/adminpanel" >Hyip Settings</a></div>
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
				<div class="block-wrapper">
					<div class="block">
						<div class='blockHeader'><h3>Config</h3><button class='button tinyBtn headerBtn' onclick="showNewRow('config_table')" ><img src='assets/img/add.png' /></button></div>
						<table id="config_table" name="config" >
							<thead> <tr> <th name='param_normalName'>Param name</th> <th name='param_value'>Param value</th> <th class="actions" ></th> </tr> </thead>
<?php
	$configs = $Core->GetConfigs();
	
	foreach ($configs as $value) {
		echo "<tr class='".$value["id"]."' >".
			 "<td name='param_normalName' >".$value["param_normalName"]."</td>".
			 "<td name='param_value' >".$value["param_value"]."</td>".
			 "<td class='actions' >".
				"<div class='editDelButtons buttonsVisible'>".
					"<button class='button tinyBtn editBtn' onclick=\"turnTableRow('config_table', '".$value["id"]."')\" ><img src='assets/img/edit.png' /></button>".
					"<button class='button tinyBtn delBtn' onclick=\"deleteRow(this, 'config_table', '".$value["id"]."')\" ><img src='assets/img/delete.png' /></button>".
				"</div>".
				"<div class='cancelSaveButtons buttonsHidden'>".
					"<button class='button tinyBtn cancelBtn' onclick='unturnTableRow()' ><img src='assets/img/cancel.png' /></button>".
					"<button class='button tinyBtn saveBtn' onclick=\"saveRow(this, 'config_table', '".$value["id"]."')\" ><img src='assets/img/save.png' /></button>".
				"</div>".
			 "</td> </tr>";
	}
?>
						</table>
					</div>
					<div class="block">
						<div class='blockHeader'><h3>Currencies</h3><button class='button tinyBtn headerBtn' onclick="showNewRow('currencies_table')" ><img src='assets/img/add.png' /></button></div>
						<table id="currencies_table" name="currencies" >
							<thead> <tr> <th name='name'>Name</th> <th name='rate_to_main'>Rate to main currency</th> <th class="actions" ></th> </tr> </thead>
<?php
	$currencies = $HyipCore->arr_GetCurrencies();
	
	foreach ($currencies as $value) {
		echo "<tr class='".$value["id"]."' >".
			 "<td name='name' >".$value["name"]."</td>".
			 "<td name='rate_to_main' >".$value["rate_to_main"]."</td>".
			 "<td class='actions' >".
				"<div class='editDelButtons buttonsVisible'>".
					"<button class='button tinyBtn editBtn' onclick=\"turnTableRow('currencies_table', '".$value["id"]."')\" ><img src='assets/img/edit.png' /></button>".
					"<button class='button tinyBtn delBtn' onclick=\"deleteRow(this, 'currencies_table', '".$value["id"]."')\" ><img src='assets/img/delete.png' /></button>".
				"</div>".
				"<div class='cancelSaveButtons buttonsHidden'>".
					"<button class='button tinyBtn cancelBtn' onclick='unturnTableRow()' ><img src='assets/img/cancel.png' /></button>".
					"<button class='button tinyBtn saveBtn' onclick=\"saveRow(this, 'currencies_table', '".$value["id"]."')\" ><img src='assets/img/save.png' /></button>".
				"</div>".
			 "</td> </tr>";
	}
?>
						</table>
					</div>
				</div>
				<div class="block-wrapper">
					<div class="block">
						<div class='blockHeader'><h3>Payment systems</h3><button class='button tinyBtn headerBtn' onclick="showNewRow('payment_systems_table')" ><img src='assets/img/add.png' /></button></div>
						<table id="payment_systems_table" name="payment_systems" >
							<thead> <tr> <th name='name'>Name</th> <th name='currency_id'>Currency</th> <th class="actions" ></th> </tr> </thead>
<?php
	$payment_systems = $HyipCore->arr_GetPaymentSystems();
	
	foreach ($payment_systems as $value) {
		echo "<tr class='".$value["id"]."' >".
			 "<td name='name' >".$value["name"]."</td>".
			 "<td name='currency_id' >".($HyipCore->s_GetCurrencyNameById($value["currency_id"]))."</td>".
			 "<td class='actions' >".
				"<div class='editDelButtons buttonsVisible'>".
					"<button class='button tinyBtn editBtn' onclick=\"turnTableRow('payment_systems_table', '".$value["id"]."')\" ><img src='assets/img/edit.png' /></button>".
					"<button class='button tinyBtn delBtn' onclick=\"deleteRow(this, 'payment_systems_table', '".$value["id"]."')\" ><img src='assets/img/delete.png' /></button>".
				"</div>".
				"<div class='cancelSaveButtons buttonsHidden'>".
					"<button class='button tinyBtn cancelBtn' onclick='unturnTableRow()' ><img src='assets/img/cancel.png' /></button>".
					"<button class='button tinyBtn saveBtn' onclick=\"saveRow(this, 'payment_systems_table', '".$value["id"]."')\" ><img src='assets/img/save.png' /></button>".
				"</div>".
			 "</td> </tr>";
	}
?>
						</table>
					</div>
					<div class="block">
						<div class='blockHeader'><h3>Referal levels</h3><button class='button tinyBtn headerBtn' onclick="showNewRow('refLevels_table')" ><img src='assets/img/add.png' /></button></div>
						<table id="refLevels_table" name="ref_levels" >
							<thead> <tr> <th name='level'>Level</th> <th name='percent'>Payout percent</th> <th class="actions" ></th> </tr> </thead>
<?php
	$refLevels = $HyipCore->arr_GetRefLevels();
	
	foreach ($refLevels as $value) {
		echo "<tr class='".$value["id"]."' >".
			 "<td name='level' >".$value["level"]."</td>".
			 "<td name='percent' >".$value["percent"]."</td>".
			 "<td class='actions' >".
				"<div class='editDelButtons buttonsVisible'>".
					"<button class='button tinyBtn editBtn' onclick=\"turnTableRow('refLevels_table', '".$value["id"]."')\" ><img src='assets/img/edit.png' /></button>".
					"<button class='button tinyBtn delBtn' onclick=\"deleteRow(this, 'refLevels_table', '".$value["id"]."')\" ><img src='assets/img/delete.png' /></button>".
				"</div>".
				"<div class='cancelSaveButtons buttonsHidden'>".
					"<button class='button tinyBtn cancelBtn' onclick='unturnTableRow()' ><img src='assets/img/cancel.png' /></button>".
					"<button class='button tinyBtn saveBtn' onclick=\"saveRow(this, 'refLevels_table', '".$value["id"]."')\" ><img src='assets/img/save.png' /></button>".
				"</div>".
			 "</td> </tr>";
	}
?>
						</table>
					</div>
				</div>
				<div class="block bigBlock">
					<div class='blockHeader'><h3>Plans</h3><button class='button tinyBtn headerBtn' onclick="showNewRow('plans_table')" ><img src='assets/img/add.png' /></button></div>
					<table id="plans_table" name="plans" >
						<thead> <tr> <th name='name'>Name</th> <th name='min'>Mininmal sum</th> <th name='max'>Maximal sum</th> <th name='percent'>Profit percent</th> <th name='delay_between_payments'>Payout interval (h)</th> <th name='payments'>Number of payouts</th> <th class="actions" ></th> </tr> </thead>
<?php
	$plans = $HyipCore->arr_GetPlans();
	
	foreach ($plans as $value) {
		echo "<tr class='".$value["id"]."' >".
			 "<td name='name' >".$value["name"]."</td>".
			 "<td name='min' >".$value["min"]."</td>".
			 "<td name='max' >".$value["max"]."</td>".
			 "<td name='percent' >".$value["percent"]."</td>".
			 "<td name='delay_between_payments' >".($value["delay_between_payments"] / 3600)."</td>".
			 "<td name='payments' >".$value["payments"]."</td>".
			 "<td class='actions' >".
				"<div class='editDelButtons buttonsVisible'>".
					"<button class='button tinyBtn editBtn' onclick=\"turnTableRow('plans_table', '".$value["id"]."')\" ><img src='assets/img/edit.png' /></button>".
					"<button class='button tinyBtn delBtn' onclick=\"deleteRow(this, 'plans_table', '".$value["id"]."')\" ><img src='assets/img/delete.png' /></button>".
				"</div>".
				"<div class='cancelSaveButtons buttonsHidden'>".
					"<button class='button tinyBtn cancelBtn' onclick='unturnTableRow()' ><img src='assets/img/cancel.png' /></button>".
					"<button class='button tinyBtn saveBtn' onclick=\"saveRow(this, 'plans_table', '".$value["id"]."')\" ><img src='assets/img/save.png' /></button>".
				"</div>".
			 "</td> </tr>";
	}
?>
					</table>
				</div>
			</div>
		</div>
	</body>
	
</html>