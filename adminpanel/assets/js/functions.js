function turnTableRow (table, row) {
	unturnTableRow();
	
	var tr = $("#" + table + " tr." + row);
	
	document.editedRow = {};
	document.editedRow.table = table;
	document.editedRow.row = row;
	document.editedRow.contents = tr.html();
	
	tr.children("td").each(
		function (index, value) {
			if ($(value).hasClass("actions")) {}
			else {
				var text = value.innerHTML;
				value.innerHTML = "<input type='text' class='field stretchy' value='" + text + "'/>";
			}
		}
	);
	tr.children("td.actions").children("div").each(
		function (index, value) {
			if ($(value).hasClass("editDelButtons")) {$(value).toggleClass("buttonsVisible").toggleClass("buttonsHidden")}
			if ($(value).hasClass("cancelSaveButtons")) {$(value).toggleClass("buttonsHidden").toggleClass("buttonsVisible")}
		}
	);
}

function unturnTableRow (table, row) {
	if (row == '0') {
		var tr = $("#" + table + " tr." + row);
		tr.detach();
		return;
	}
	
	if (document.editedRow) {
		var table = document.editedRow.table;
		var row = document.editedRow.row;
		
		var tr = $("#" + table + " tr." + row);
		
		tr.html(document.editedRow.contents);
	}
}

function fillIdsInRow(table, row, id) {
	var tr = $("#" + table + " tr." + row);
	tr.toggleClass("0").toggleClass(id);
	tr.children("td").each(
		function (index, value) {
			if ($(value).hasClass("actions")) {
				$(value).children("div.cancelSaveButtons").children("button.cancelBtn").attr("onclick", "unturnTableRow()");
				$(value).children("div.cancelSaveButtons").children("button.saveBtn").attr("onclick", "saveRow(this, '" + table + "', '" + id + "')");
				$(value).children("div.editDelButtons").children("button.editBtn").attr("onclick", "turnTableRow('" + table + "', '" + id + "')");
				$(value).children("div.editDelButtons").children("button.delBtn").attr("onclick", "deleteRow(this, '" + table + "', '" + id + "')");
			}
		}
	);
}

