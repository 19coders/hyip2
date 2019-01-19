function saveRow(btn, table, row) {
	var btnContent = btn.innerHTML;
	btn.innerHTML = "<img heiht='21' width='21' src='assets/img/loading.png' />";
	
	var url = "/adm/editRow?table=" + $("#" + table).attr("name");
	url += "&id=" + row;
	
	var fieldsAndValues = {};
	
	var tr = $("#" + table + " tr." + row);
	
	tr.children("td").each(
		function (index, value) {
			if ($(value).hasClass("actions")) {}
			else {
				fieldsAndValues[$(value).attr("name")] = $(value).children("input").val();
			}
		}
	);
	
	url += "&fieldsAndValues=" + JSON.stringify(fieldsAndValues);
	url = encodeURI(url);
	
	$.get(
		url,
		function (data) {
			tr.children("td").each(
				function (index, value) {
					if ($(value).hasClass("actions")) {}
					else {
						var text = $(value).children("input").val();
						value.innerHTML = text;
					}
				}
			);
			tr.children("td.actions").children("div").each(
				function (index, value) {
					if ($(value).hasClass("editDelButtons")) {$(value).toggleClass("buttonsVisible").toggleClass("buttonsHidden")}
					if ($(value).hasClass("cancelSaveButtons")) {$(value).toggleClass("buttonsHidden").toggleClass("buttonsVisible")}
				}
			);
			document.editedRow = {};
			
			if (data.length) fillIdsInRow(table, row, data);
			
			btn.innerHTML = btnContent;
		}
	);
}

function deleteRow(btn, table, row) {
	
	unturnTableRow();
	
	var btnContent = btn.innerHTML;
	btn.innerHTML = "<img heiht='21' width='21' src='assets/img/loading.png' />";
	
	var url = "/adm/deleteRow?table=" + $("#" + table).attr("name");
	url += "&id=" + row;
	
	$.get(
		url,
		function (data) {
			if (data.length) { 
				unturnTableRow();
				btn.innerHTML = btnContent;
			}
			else {
				document.editedRow = {};
				btn.innerHTML = btnContent;
				
				var tr = $("#" + table + " tr." + row);
				tr.detach();
			}
		}
	);
}
function showNewRow(tableId) {
	unturnTableRow();
	
	var table = document.getElementById(tableId);
	var ths = $(table).find("thead tr th");
	
	var newTr = document.createElement('tr');
	newTr.className = "0";
	var newTrHTML = "";
	
	ths.each(
		function (index, value) {
			if ($(value).hasClass("actions")) {
				
				newTrHTML += 
				"<td class='actions' >"+
					"<div class='editDelButtons buttonsHidden'>"+
						"<button class='button tinyBtn editBtn' onclick=\"turnTableRow('" + tableId + "', '0')\" ><img src='assets/img/edit.png' /></button>"+
						"<button class='button tinyBtn delBtn' onclick=\"deleteRow(this, '" + tableId + "', '0')\" ><img src='assets/img/delete.png' /></button>"+
					"</div>"+
					"<div class='cancelSaveButtons buttonsVisible'>"+
						"<button class='button tinyBtn cancelBtn' onclick=\"unturnTableRow('" + tableId + "', '0')\" ><img src='assets/img/cancel.png' /></button>"+
						"<button class='button tinyBtn saveBtn' onclick=\"saveRow(this, '" + tableId + "', '0')\" ><img src='assets/img/save.png' /></button>"+
					"</div>"+
				"</td >";
			
			}
			else {
				var name = $(value).attr("name");
				newTrHTML += "<td name='" + name + "'><input type='text' class='field stretchy' value=''/></td>";
			}
		}
	);
	
	newTr.innerHTML = newTrHTML;
	
	$(table).append(newTr);
}

function checkNewMessageForm() {
	var form = document.getElementById("newMessage_form");
	if (form.text.value == "") { alert("Enter message text!"); return false; }
	else return true;
}