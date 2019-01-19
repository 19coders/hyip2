function myBind(func, context) {
	return function() {
		return func.apply(context, arguments);
	};
}

function getData(url, callback) {
	$.get(
		url,
		function (JSONdata) {
			var data = JSON.parse(JSONdata);
			
			callback(data);
		}
	);
}

function toggleModalLoading(modalFadeId) {
	var elem = $('#' + modalFadeId + ' .modal .modal-loading');
	var elem2 = $('#' + modalFadeId + ' .modal .modal-content');
	
	var disp = elem.css("display");
	if (disp == "block") {
		elem.css("display", "none");
		elem2.css("display", "block");
	}
	else if (disp == "none"){
		elem.css("display", "block");
		elem2.css("display", "none");
	}
}

function toggleBodyOverflow() {
	var ovf = $("body").css("overflow");
	if (ovf == "hidden") {
	//	$("body").css("paddingRight", "0px");
		$("body").css("overflow", "auto");
	}
	else if (ovf == "auto") {
	//	$("body").css("paddingRight", "17px");
		$("body").css("overflow", "hidden");
	}
}

function toggleBlockDisplay(modalFade) {
	var disp = modalFade.css("display");
	if (disp == "block") modalFade.css("display", "none");
	else if (disp == "none") modalFade.css("display", "block");
}

function toggleBlockOpasity(modalFade) {
	var opas = modalFade.css("opacity");
	
	if (opas == 1) {
		modalFade.css("opacity", 0);
		setTimeout(function() {
			toggleBlockDisplay(modalFade);
		}, 200);
	}
	else if (opas == 0) {
		toggleBlockDisplay(modalFade);
		setTimeout(function() {
			modalFade.css("opacity", 1);
		}, 100);
	}
}

function toggleDisplayModal(modalFadeId) {
	toggleBodyOverflow();
	toggleBlockOpasity($('#' + modalFadeId));
}

function toggleDisplayNotice(noticeFadeId) {
	toggleBlockOpasity($('#' + noticeFadeId));
}

function showNotice(text) {
	$("#notice-text").html(text);
	toggleDisplayNotice('notice-fade');
}

function searchArrInStr(str, arr) {
	var arrRegExp = new RegExp(arr.join('|'));
	 
	if (arrRegExp.test(str)) return true;
	else return false;
}
