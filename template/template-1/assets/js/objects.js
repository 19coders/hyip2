window.CustomSelect = {
	id: "",
	name: "",
	fill: function(data) {
		var name = this.name;
		var id = this.id;
		
		var dataForSelect = "<input type='radio' name='" + name + "' value=''><div>";
		data.forEach(
			function(item, i, arr) {
				dataForSelect += "<input type='radio' name='" + name + "' value='" + item["id"] + "' id='" + id + "_" + name + item["id"] + "' />";
				dataForSelect += "<label for='" + id + "_" + name + item["id"] + "' >" + item["name"] + "</label>";
			}
		);
		dataForSelect += "</div>";
		
		$("#" + this.id).html(dataForSelect);
		
	}
}

window.CustomPrompt = {
	txt: "",
	result: "",
	success: function() {},
	hide: function() {
	},
	show: function() {
		$("#field-text").html(this.txt);
		toggleDisplayNotice('field-fade');
		$("#field-fade .darkBtn").off("click");
		$("#field-fade .darkBtn").click(
			myBind(
				function () {
					this.result = $("#field-fade .lightField").val();
					this.hide();
				}, 
				window.CustomPrompt
			)
		);
	}
}