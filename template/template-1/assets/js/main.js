function setWallets(modalFadeId) {
	toggleModalLoading(modalFadeId);
	$.get(
		"/template-1/ajax/getWallets.php",
		function (JSONdata) {
			var modalContent = $('#' + modalFadeId + ' .modal .modal-content');
			
			var data = JSON.parse(JSONdata);
			
			var wallets = "";
			
			data.forEach (
				function(item, i, arr) {
					wallets += "<tr> <td>" + item["payment_system_name"] + "</td> <td>" + item["wallet"] + "</td> <td>" + 
					
						"<form method='post' class='tinyForm' id='editWallet-" + item["id"] + "_form' >" +
							"<input type='hidden' name='wid' value='" + item["id"] + "' />" +
							"<input type='hidden' name='wallet' value='' />" +
							"<input type='hidden' name='do' value='editWallet' />" +
							"<button class='darkBtn tinyBtn' type='button' onclick='editWallet(" + item["id"] + ");' >" +
								"<img src='template-1/assets/images/edit.png' />" +
							"</button>" +
						"</form>" +
					
						"<form method='post' class='tinyForm' >" +
							"<input type='hidden' name='wid' value='" + item["id"] + "' />" +
							"<button class='darkBtn tinyBtn' type='submit' name='do' value='delWallet' >" +
								"<img src='template-1/assets/images/delete.png' />" +
							"</button>" +
						"</form>" + 
						
					"</td> </tr>";
				}
			);
			
			var content = 
			"\
\
						<table cellspacing='0' class='smallTable'>\
							<thead> <tr> <th>Платежная система</th> <th>Кошелек</th> <th>Действия</th> </tr> </thead>\
							" + wallets +
							"\
						</table>\
			";
			
			toggleModalLoading(modalFadeId);
			modalContent.html(content);
		}
	);
}

function editWallet(wid) {
	window.CustomPrompt.formId = 'editWallet-' + wid + '_form';
	window.CustomPrompt.text = "Введите кошелек:";
	window.CustomPrompt.success = function() {
		if (this.result.length > 200) {
			showNotice("Слишком длинный кошелек!");
			return;
		}
		var form = window.getElementById(this.formId);
		form.wallet.value = this.result;
		form.submit();
	}
	window.CustomPrompt.close = function() {
		var text = $("#field-fade .lightField").val();
		if (text == "") showNotice("Введите кошелек!");
		else this.success();
	}
	
	window.CustomPrompt.open();
}

function checkAddWalletForm(form) {
	if (form.payment_system.value == "") {
		showNotice("Выберите платежную систему!");
		return false;
	}
	else if (form.wallet.value == "") {
		showNotice("Введите кошелек!");
		return false;
	}
	else return true;
}

function checkAddDepositForm(form) {
	if (form.currency.value == "") {
		showNotice("Выберите валюту!");
		return false;
	}
	else if (form.plan.value == "") {
		showNotice("Выберите план!");
		return false;
	}
	else if (form.sum.value == "") {
		showNotice("Введите сумму!");
		return false;
	}
	else return true;
}

function checkAddTicketForm(form){
	if (form.title.value == "") {
		showNotice("Заголовок не может быть пустым!");
		return false;
	}
	else if (form.title.value.length > 200) {
		showNotice("Слишком длинный заголовок!");
		return false;
	}
	else if (form.text.value == "") {
		showNotice("Сообщение не может быть пустым!");
		return false;
	}
	else return true;
}

function checkHelpForm(form){
	if (form.email.value == "") {
		showNotice("Email не может быть пустым!");
		return false;
	}
	else if (form.email.value.indexOf("@") == -1 || form.email.value.indexOf(".") == -1) {
		showNotice("Вы уверены, что вы ввели верный email?");
		return false;
	}
	else if (form.title.value == "") {
		showNotice("Заголовок не может быть пустым!");
		return false;
	}
	else if (form.title.value.length > 200) {
		showNotice("Слишком длинный заголовок!");
		return false;
	}
	else if (form.text.value == "") {
		showNotice("Сообщение не может быть пустым!");
		return false;
	}
	else return true;
}

function checkNewMessageForm(form){
	if (form.text.value == "") {
		showNotice("Сообщение не может быть пустым!");
		return false;
	}
	else return true;
}

function checkRegForm(form) {
	if (form.login.value == "") {
		showNotice("Имя пользователя не может быть пустым!");
		return false;
	}
	else if (form.login.value.length > 22) {
		showNotice("Слишком длинное имя пользователя!");
		return false;
	}
	else if (searchArrInStr(form.login.value, ['#', '@', '"', '\'', '%', '№', '\\&', '\\?', ',', '\\*', '\\^', ';', '`'])) {
		showNotice("В имени пользователя используются недопустимые символы!");
		return false;
	}
	else if (form.email.value == "") {
		showNotice("E-mail не может быть пустым!");
		return false;
	}
	else if (form.email.value.indexOf("@") == -1 || form.email.value.indexOf(".") == -1) {
		showNotice("Вы уверены, что вы ввели верный email?");
		return false;
	}
	else if (form.password.value == "") {
		showNotice("Пароль не может быть пустым!");
		return false;
	}
	else if (form.password_repeat.value == "") {
		showNotice("Повторите пароль!");
		return false;
	}
	else if (form.termsAgreed.checked == false) {
		showNotice("Вы должны принять условия соглашения!");
		return false;
	}
	else return true;
}

function checkWithdrawalForm(form) {
	if (form.wallet.value == "") {
		showNotice("Выберите кошелек!");
		return false;
	}
	else if (form.sum.value == "") {
		showNotice("Введите сумму!");
		return false;
	}
	else if (form.sum.value == "0") {
		showNotice("Сумма не может равняться нулю!");
		return false;
	}
	else return true;
}

function Calc(form) {
	var percent = form.plan.value;
	var amount = form.sum.value;
	
	var res = amount / 100 * percent;
	res = Math.round(res * 100) / 100;
	
	$("#calc .calc-result").html("<br />В конце депозита вы получите " + res + " рублей.");
	
	return false;
}


	function QiwiHandler() {
		window.CustomPrompt.txt = "Переведите деньги на такой-то счет. Ниже введите идентификатор транзакции. Если перевод действительно был, деньги зачислятся на ваш счет.";
		window.CustomPrompt.success = function() {
			var form = document.createElement('form');
			$(form).attr("method", "post");
			
			$(form).append(
				"<input type='hidden' name='payment_system' value='1'>" +
				"<input type='hidden' name='trans_id' value='" + this.result + "'>" +
				"<input type='hidden' name='do' value='setTransId'>"
			);
			
			$('body').append(form);
			$(form).submit();
		}
		window.CustomPrompt.hide = function() {
			var txt = $("#field-fade .lightField").val();
			if (txt == "") showNotice("Введите идентификатор транзакции!");
			else this.success();
		}
		
		window.CustomPrompt.show();
	}
	function PayeerHandler() {
		window.CustomPrompt.txt = "Переведите деньги на такой-то счет. Ниже введите идентификатор транзакции. Если перевод действительно был, деньги зачислятся на ваш счет.";
		window.CustomPrompt.success = function() {
			var form = document.createElement('form');
			$(form).attr("method", "post");
			
			$(form).append(
				"<input type='hidden' name='payment_system' value='2'>" +
				"<input type='hidden' name='trans_id' value='" + this.result + "'>" +
				"<input type='hidden' name='do' value='setTransId'>"
			);
			
			$('body').append(form);
			$(form).submit();
		}
		window.CustomPrompt.hide = function() {
			var txt = $("#field-fade .lightField").val();
			if (txt == "") showNotice("Введите идентификатор транзакции!");
			else this.success();
		}
		
		window.CustomPrompt.show();
	}
	function BitcoinHandler() {}
	
	function handlePaymentSystem(form){
		if (form.payment_system.value == "") {
			showNotice("Выберите платежную систему!");
			return;
		}
		
		switch (form.payment_system.value) {
			case "1": //Qiwi
				QiwiHandler();
			break;
			
			case "2": //Payeer
				PayeerHandler();
			break;
			
			case "3": //Bitcoin
				BitcoinHandler();
			break;
			
			default: break;
		}
	}
	
	